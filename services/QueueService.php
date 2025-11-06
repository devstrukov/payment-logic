<?php
namespace app\services;

use app\queue\QueueInterface;

class QueueService
{
    private $queue;
    private $notificationManager;

    public function __construct(QueueInterface $queue, NotificationManager $notificationManager)
    {
        $this->queue = $queue;
        $this->notificationManager = $notificationManager;
    }

    public function pushPaymentJob(int $paymentId): bool
    {
        \Yii::info("Pushing payment job to queue: payment_id = {$paymentId}");
        $result = $this->queue->push('payment-processing', [
            'payment_id' => $paymentId,
            'created_at' => time()
        ]);
        \Yii::info("Queue push result: " . ($result ? 'success' : 'failed'));
        return $result;
    }

    public function pushNotificationJob(string $type, string $message, string $recipient): bool
    {
        return $this->queue->push('notifications', [
            'type' => $type,
            'message' => $message,
            'recipient' => $recipient,
            'created_at' => time()
        ]);
    }

    public function processPaymentQueue(): void
    {
        \Yii::info("Starting payment queue processing...");

        $this->queue->process('payment-processing', function ($data) {
            \Yii::info("Processing queue job: " . json_encode($data));

            if (isset($data['payment_id'])) {
                $paymentId = $data['payment_id'];
                \Yii::info("Processing payment from queue: {$paymentId}");

                try {
                    $paymentService = \Yii::$container->get(\app\services\PaymentServiceInterface::class);
                    $paymentService->processPayment($paymentId);
                    \Yii::info("Payment {$paymentId} processed successfully from queue");
                } catch (\Exception $e) {
                    \Yii::error("Error processing payment {$paymentId} from queue: " . $e->getMessage());
                }
            } else {
                \Yii::warning("Invalid queue job data: " . json_encode($data));
            }
        });

        \Yii::info("Payment queue processing finished");
    }

    public function processNotificationQueue(): void
    {
        \Yii::info("Starting notification queue processing...");

        $this->queue->process('notifications', function ($data) {
            \Yii::info("Processing notification job: " . json_encode($data));

            if (isset($data['type'], $data['message'], $data['recipient'])) {
                try {
                    $this->notificationManager->notifyAll(
                        $data['message'],
                        $data['recipient'],
                        $data['type'] === 'all' ? null : $data['type']
                    );
                    \Yii::info("Notification sent successfully");
                } catch (\Exception $e) {
                    \Yii::error("Error sending notification: " . $e->getMessage());
                }
            } else {
                \Yii::warning("Invalid notification job data: " . json_encode($data));
            }
        });

        \Yii::info("Notification queue processing finished");
    }
}