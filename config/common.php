<?php

use app\components\Env;

Env::load(__DIR__ . '/../.env');

// Загружаем DI контейнер
$container = require __DIR__ . '/containers.php';

return [
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
    ],
    'params' => [
        'yooMoney' => [
            'apiKey' => Env::get('YOO_MONEY_API_KEY'),
        ],
        'webMoney' => [
            'certPath' => Env::get('WEBMONEY_CERT_PATH'),
            'keyPath' => Env::get('WEBMONEY_KEY_PATH'),
        ],
    ],
    'container' => $container, // Добавляем контейнер в конфиг
];