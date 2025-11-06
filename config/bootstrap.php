<?php
use app\listeners\PaymentNotificationListener;

// Регистрируем обработчики событий
Yii::$app->on('payment.created', [Yii::$container->get(PaymentNotificationListener::class), 'handlePaymentCreated']);
Yii::$app->on('payment.succeeded', [Yii::$container->get(PaymentNotificationListener::class), 'handlePaymentSucceeded']);
Yii::$app->on('payment.failed', [Yii::$container->get(PaymentNotificationListener::class), 'handlePaymentFailed']);