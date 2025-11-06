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
    private $queueService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepo,
        AttemptRepositoryInterface $attemptRepo,
        PaymentProviderInterface $paymentProvider,
        UserServiceInterface $userService,
        \app\services\QueueService $queueService
    ) {
        $this->paymentRepo = $paymentRepo;
        $this->attemptRepo = $attemptRepo;
        $this->paymentProvider = $paymentProvider;
        $this->userService = $userService;
        $this->queueService = $queueService;
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

        // Добавляем в очередь вместо синхронной обработки
        $this->queueService->pushPaymentJob($payment->id);

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
        // ... код без изменений (оставляем как было)
    }

    // Новый метод для обработки платежа из очереди
    public function processPayment(int $paymentId): void
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

            \Yii::info("Payment {$paymentId} processed successfully");

        } catch (\Exception $e) {
            // Обновляем попытку и платеж
            $lastAttempt->markAsFailed($e->getMessage());
            $payment->markAsFailed();

            \Yii::error("Payment {$paymentId} failed: " . $e->getMessage());
        }
    }
}