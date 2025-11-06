<?php
namespace app\services;

use Yii;

class SmsNotificationService implements NotificationServiceInterface
{
    private $apiKey;
    private $sender;

    public function __construct(string $apiKey = '', string $sender = 'Payments')
    {
        $this->apiKey = $apiKey;
        $this->sender = $sender;
    }

    public function notify(string $message, string $recipient): bool
    {
        Yii::info("Sending SMS to {$recipient}: {$message}");

        // Заглушка - в реальности API вызов к SMS провайдеру
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post('https://sms-provider.com/send', [
        //     'json' => [
        //         'api_key' => $this->apiKey,
        //         'to' => $recipient,
        //         'message' => $message,
        //         'sender' => $this->sender
        //     ]
        // ]);

        // Для демонстрации просто логируем
        Yii::info("SMS: To: {$recipient}, Message: {$message}");
        return true;
    }

    public function canHandle(string $type): bool
    {
        return $type === 'sms';
    }
}