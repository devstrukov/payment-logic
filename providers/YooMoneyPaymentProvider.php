<?php
namespace app\providers;

use Yii;

class YooMoneyPaymentProvider implements PaymentProviderInterface
{
    private $apiKey;
    private $integrationId = 1; // ID интеграции YooMoney в базе

    public function __construct()
    {
        $this->apiKey = Yii::$app->params['yooMoney']['apiKey'] ?? 'test_key';
    }

    public function getIntegrationId(): int
    {
        return $this->integrationId;
    }

    public function getBalance(): float
    {
        Yii::info("Using YooMoney API key: " . substr($this->apiKey, 0, 8) . '...');

        // Заглушка - в реальности API запрос к YooMoney
        return 10000.50;
    }

    public function processPayment(array $data): array
    {
        // Имитация API вызова к YooMoney
        sleep(1);

        if (rand(1, 10) > 2) {
            return [
                'external_id' => 'ym_' . uniqid(),
                'status' => 'success'
            ];
        }

        throw new \RuntimeException('YooMoney API error: insufficient funds');
    }
}