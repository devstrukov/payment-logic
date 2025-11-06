<?php
namespace app\repositories;

use app\models\Attempt;
use app\models\Payment;

interface AttemptRepositoryInterface
{
    public function createForPayment(Payment $payment, array $data): Attempt;
    public function save(Attempt $attempt): bool;
}