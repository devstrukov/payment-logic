<?php
use yii\di\Container;

$container = \Yii::$container;

// Репозитории
$container->set('app\repositories\PaymentRepositoryInterface', 'app\repositories\PaymentRepository');
$container->set('app\repositories\AttemptRepositoryInterface', 'app\repositories\AttemptRepository');

// Провайдеры (по умолчанию YooMoney)
$container->set('app\providers\PaymentProviderInterface', 'app\providers\YooMoneyPaymentProvider');

// Очереди (по умолчанию Redis)
$container->set('app\queue\QueueInterface', function() {
    return new \app\queue\RedisQueue(\Yii::$app->redis);
});

// Сервисы
$container->set('app\services\UserServiceInterface', 'app\services\UserService');
$container->set('app\services\PaymentServiceInterface', 'app\services\PaymentService');
$container->set('app\services\QueueService', 'app\services\QueueService');
$container->set('app\services\IntegrationService', 'app\services\IntegrationService');

return $container;