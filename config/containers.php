<?php
use yii\di\Container;

$container = \Yii::$container;

// Репозитории
$container->set('app\repositories\PaymentRepositoryInterface', 'app\repositories\PaymentRepository');
$container->set('app\repositories\AttemptRepositoryInterface', 'app\repositories\AttemptRepository');

// Провайдеры
$container->set('app\providers\PaymentProviderInterface', 'app\providers\YooMoneyPaymentProvider');

// Сервисы
$container->set('app\services\UserServiceInterface', 'app\services\UserService');
$container->set('app\services\PaymentServiceInterface', 'app\services\PaymentService');

return $container;