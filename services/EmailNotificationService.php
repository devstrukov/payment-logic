<?php
namespace app\services;

use Yii;

class EmailNotificationService implements NotificationServiceInterface
{
    private $fromEmail;

    public function __construct(string $fromEmail = 'noreply@example.com')
    {
        $this->fromEmail = $fromEmail;
    }

    public function notify(string $message, string $recipient): bool
    {
        Yii::info("Sending email to {$recipient}: {$message}");

        // Заглушка - в реальности отправка email через SwiftMailer и т.д.
        // return Yii::$app->mailer->compose()
        //     ->setFrom($this->fromEmail)
        //     ->setTo($recipient)
        //     ->setSubject('Payment Notification')
        //     ->setTextBody($message)
        //     ->send();

        // Для демонстрации просто логируем
        Yii::info("EMAIL: To: {$recipient}, Message: {$message}");
        return true;
    }

    public function canHandle(string $type): bool
    {
        return $type === 'email';
    }
}