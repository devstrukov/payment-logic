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

    public function actionTestQueue()
    {
        $result = $this->queueService->pushPaymentJob(999);
        echo "Test job pushed: " . ($result ? 'success' : 'failed') . "\n";
    }
}