<?php
namespace app\repositories;

use app\models\Payment;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;
    public function save(Payment $payment): bool;
    public function create(array $data): Payment;
}