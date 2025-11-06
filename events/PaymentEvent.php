<?php
namespace app\events;

use yii\base\Event;
use app\models\Payment;

class PaymentEvent extends Event
{
    public Payment $payment;

    public function __construct(Payment $payment, $config = [])
    {
        parent::__construct($config);
        $this->payment = $payment;
    }
}