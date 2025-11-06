<?php
use app\listeners\PaymentNotificationListener;

return [
    'payment.created' => [
        [PaymentNotificationListener::class, 'handlePaymentCreated'],
    ],
    'payment.succeeded' => [
        [PaymentNotificationListener::class, 'handlePaymentSucceeded'],
    ],
    'payment.failed' => [
        [PaymentNotificationListener::class, 'handlePaymentFailed'],
    ],
];