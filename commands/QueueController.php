<?php
namespace app\commands;

use yii\console\Controller;
use app\services\QueueService;

class QueueController extends Controller
{
    private $queueService;

    public function __construct($id, $module, QueueService $queueService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->queueService = $queueService;
    }

    public function actionProcessPayments()
    {
        echo "Starting payment queue processing...\n";

        try {
            $this->queueService->processPaymentQueue();
        } catch (\Exception $e) {
            echo "Queue processing error: " . $e->getMessage() . "\n";
            \Yii::error("Queue processing error: " . $e->getMessage());
        }

        echo "Payment queue processing finished.\n";
    }

    public function actionProcessNotifications()
    {
        echo "Starting notification queue processing...\n";

        try {
            $this->queueService->processNotificationQueue();
        } catch (\Exception $e) {
            echo "Notification queue processing error: " . $e->getMessage() . "\n";
            \Yii::error("Notification queue processing error: " . $e->getMessage());
        }

        echo "Notification queue processing finished.\n";
    }

    public function actionProcessAll()
    {
        echo "Starting all queue processing...\n";

        // Обрабатываем платежи
        $this->queueService->processPaymentQueue();

        // Обрабатываем уведомления
        $this->queueService->processNotificationQueue();

        echo "All queue processing finished.\n";
    }

    public function actionTestNotification()
    {
        $result = $this->queueService->pushNotificationJob(
            'all',
            'Test notification message',
            'test@example.com'
        );

        echo "Test notification job pushed: " . ($result ? 'success' : 'failed') . "\n";
    }
}