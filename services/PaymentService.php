<?php
namespace app\services;

use app\models\Payment;
use app\models\Attempt;
use app\repositories\PaymentRepositoryInterface;
use app\repositories\AttemptRepositoryInterface;
use app\providers\PaymentProviderInterface;

class PaymentService implements PaymentServiceInterface
{
    private $paymentRepo;
    private $attemptRepo;
    private $paymentProvider;
    private $userService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepo,
        AttemptRepositoryInterface $attemptRepo,
        PaymentProviderInterface $paymentProvider,
        UserServiceInterface $userService
    ) {
        $this->paymentRepo = $paymentRepo;
        $this->attemptRepo = $attemptRepo;
        $this->paymentProvider = $paymentProvider;
        $this->userService = $userService;
    }

    public function createPayment(array $data): array
    {
        // Валидация суммы
        if (empty($data['amount'])) {
            throw new \InvalidArgumentException('Amount is required');
        }

        $amount = (float)$data['amount'];

        // Проверяем что у пользователя достаточно средств
        if (!$this->userService->validateUserHasSufficientBalance($amount)) {
            throw new \RuntimeException('Insufficient balance');
        }

        // Получаем текущего пользователя
        $currentUserId = $this->userService->getCurrentUserId();

        // Создаем платеж
        $payment = $this->paymentRepo->create([
            'user_id' => $currentUserId,
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING
        ]);

        // Получаем ID интеграции от провайдера
        $integrationId = $this->paymentProvider->getIntegrationId();

        // Создаем pending попытку
        $this->attemptRepo->createForPayment($payment, [
            'integration_id' => $integrationId,
            'status' => Attempt::STATUS_PENDING
        ]);

        // Синхронная обработка (в коммите 3 заменим на очередь)
        $this->processPayment($payment->id);

        return [
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'user_id' => $payment->user_id,
            'created_at' => $payment->created_at
        ];
    }

    public function getPaymentStatus(int $paymentId): array
    {
        $payment = $this->paymentRepo->findById($paymentId);

        if (!$payment) {
            throw new \RuntimeException('Payment not found');
        }

        // Проверяем что платеж принадлежит текущему пользователю
        $currentUserId = $this->userService->getCurrentUserId();
        if ($payment->user_id !== $currentUserId) {
            throw new \RuntimeException('Payment not found');
        }

        $attemptsData = [];
        foreach ($payment->attempts as $attempt) {
            $attemptsData[] = [
                'id' => $attempt->id,
                'status' => $attempt->status,
                'integration_id' => $attempt->integration_id,
                'external_id' => $attempt->external_id,
                'error_message' => $attempt->error_message,
                'created_at' => $attempt->created_at
            ];
        }

        return [
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'user_id' => $payment->user_id,
            'attempts' => $attemptsData,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ];
    }

    private function processPayment(int $paymentId): void
    {
        $payment = $this->paymentRepo->findById($paymentId);
        $lastAttempt = $payment->lastAttempt;

        if (!$lastAttempt || $lastAttempt->isCompleted()) {
            return; // Уже обработано
        }

        try {
            $result = $this->paymentProvider->processPayment([
                'amount' => $payment->amount,
                'user_id' => $payment->user_id
            ]);

            // Обновляем попытку и платеж
            $lastAttempt->markAsSucceed($result['external_id']);
            $payment->markAsSucceed();

        } catch (\Exception $e) {
            // Обновляем попытку и платеж
            $lastAttempt->markAsFailed($e->getMessage());
            $payment->markAsFailed();

            throw $e;
        }
    }
}