<?php
use yii\di\Container;
use app\services\NotificationManager;
use app\services\EmailNotificationService;
use app\services\SmsNotificationService;
use app\listeners\PaymentNotificationListener;

$container = \Yii::$container;

// Репозитории
$container->set('app\repositories\PaymentRepositoryInterface', 'app\repositories\PaymentRepository');
$container->set('app\repositories\AttemptRepositoryInterface', 'app\repositories\AttemptRepository');

// Провайдеры
$container->set('app\providers\PaymentProviderInterface', 'app\providers\YooMoneyPaymentProvider');

// Очереди
$container->set('app\queue\QueueInterface', function() {
    return new \app\queue\RedisQueue(\Yii::$app->redis);
});

// Сервисы уведомлений
$container->set('app\services\EmailNotificationService', function() {
    return new EmailNotificationService(
        \Yii::$app->params['notifications']['email_from']
    );
});

$container->set('app\services\SmsNotificationService', function() {
    return new SmsNotificationService(
        \Yii::$app->params['notifications']['sms_api_key'],
        \Yii::$app->params['notifications']['sms_sender']
    );
});

$container->set('app\services\NotificationManager', function() {
    $manager = new NotificationManager();
    $manager->addService(\Yii::$container->get('app\services\EmailNotificationService'));
    $manager->addService(\Yii::$container->get('app\services\SmsNotificationService'));
    return $manager;
});

// Слушатели событий
$container->set('app\listeners\PaymentNotificationListener', function() {
    return new PaymentNotificationListener(
        \Yii::$container->get('app\services\NotificationManager')
    );
});

// Основные сервисы
$container->set('app\services\UserServiceInterface', 'app\services\UserService');
$container->set('app\services\PaymentServiceInterface', 'app\services\PaymentService');
$container->set('app\services\AuthServiceInterface', 'app\services\AuthService');
$container->set('app\services\QueueService', 'app\services\QueueService');
$container->set('app\services\IntegrationService', 'app\services\IntegrationService');

return $container;