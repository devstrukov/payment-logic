<?php

use app\components\Env;
use app\listeners\PaymentNotificationListener;
use app\services\NotificationManager;
use app\services\EmailNotificationService;
use app\services\SmsNotificationService;

Env::load(__DIR__ . '/../.env');

// Загружаем DI контейнер
$container = require __DIR__ . '/containers.php';

// Загружаем конфигурацию событий
$events = require __DIR__ . '/events.php';

return [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'bootstrap'],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => Env::get('DB_DSN', 'mysql:host=localhost;dbname=payments'),
            'username' => Env::get('DB_USERNAME', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset' => Env::get('DB_CHARSET', 'utf8'),
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => Env::get('REDIS_HOST', 'localhost'),
            'port' => Env::get('REDIS_PORT', 6379),
            'database' => Env::get('REDIS_DATABASE', 0),
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
    ],
    'params' => [
        'yooMoney' => [
            'apiKey' => Env::get('YOO_MONEY_API_KEY'),
        ],
        'webMoney' => [
            'certPath' => Env::get('WEBMONEY_CERT_PATH'),
            'keyPath' => Env::get('WEBMONEY_KEY_PATH'),
        ],
        'notifications' => [
            'email_from' => Env::get('EMAIL_FROM', 'noreply@example.com'),
            'sms_api_key' => Env::get('SMS_API_KEY', ''),
            'sms_sender' => Env::get('SMS_SENDER', 'Payments'),
        ],
    ],
    'container' => $container,
    'events' => $events, // Добавляем события в конфиг
];