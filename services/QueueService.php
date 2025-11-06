<?php
namespace app\services;

use app\queue\QueueInterface;

class QueueService
{
    private $queue;

    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    public function pushPaymentJob(int $paymentId): bool
    {
        return $this->queue->push('payment-processing', [
            'payment_id' => $paymentId,
            'created_at' => time()
        ]);
    }

    public function processPaymentQueue(): void
    {
        $this->queue->process('payment-processing', function ($data) {
            if (isset($data['payment_id'])) {
                $paymentService = \Yii::$container->get(\app\services\PaymentServiceInterface::class);
                $paymentService->processPayment($data['payment_id']);
            }
        });
    }
}