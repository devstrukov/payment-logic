<?php
namespace app\listeners;

use yii\base\Event;
use app\events\PaymentCreatedEvent;
use app\events\PaymentSucceededEvent;
use app\events\PaymentFailedEvent;
use app\services\NotificationManager;
use Yii;

class PaymentNotificationListener
{
    private $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    public function handlePaymentCreated(PaymentCreatedEvent $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        $message = "Payment #{$payment->id} for {$payment->amount} has been created and is being processed.";

        // Отправляем уведомление через очередь
        Yii::$app->queue->push('notifications', [
            'type' => 'email', // или можно из настроек пользователя
            'message' => $message,
            'recipient' => $user->email
        ]);

        Yii::info("Payment created notification queued for payment #{$payment->id}");
    }

    public function handlePaymentSucceeded(PaymentSucceededEvent $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        $message = "Payment #{$payment->id} for {$payment->amount} has been completed successfully.";

        Yii::$app->queue->push('notifications', [
            'type' => 'all', // отправляем все типы уведомлений
            'message' => $message,
            'recipient' => $user->email
        ]);

        Yii::info("Payment succeeded notification queued for payment #{$payment->id}");
    }

    public function handlePaymentFailed(PaymentFailedEvent $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        $lastAttempt = $payment->lastAttempt;
        $error = $lastAttempt ? $lastAttempt->error_message : 'Unknown error';

        $message = "Payment #{$payment->id} for {$payment->amount} has failed. Error: {$error}";

        Yii::$app->queue->push('notifications', [
            'type' => 'all',
            'message' => $message,
            'recipient' => $user->email
        ]);

        Yii::info("Payment failed notification queued for payment #{$payment->id}");
    }
}