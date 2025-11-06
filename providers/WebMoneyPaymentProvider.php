<?php
namespace app\providers;

use Yii;

class WebMoneyPaymentProvider implements PaymentProviderInterface
{
    private $certPath;
    private $keyPath;
    private $integrationId = 2; // ID интеграции WebMoney в базе

    public function __construct()
    {
        $this->certPath = Yii::$app->params['webMoney']['certPath'] ?? '';
        $this->keyPath = Yii::$app->params['webMoney']['keyPath'] ?? '';
    }

    public function getIntegrationId(): int
    {
        return $this->integrationId;
    }

    public function getBalance(): float
    {
        Yii::info("Using WebMoney cert: {$this->certPath}");

        // Заглушка - в реальности API запрос к WebMoney
        return 7500.25;
    }

    public function processPayment(array $data): array
    {
        sleep(1);

        if (rand(1, 10) > 3) {
            return [
                'external_id' => 'wm_' . uniqid(),
                'status' => 'completed'
            ];
        }

        throw new \RuntimeException('WebMoney API error: transaction declined');
    }
}