<?php
namespace app\repositories;

use app\models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::findOne($id);
    }

    public function save(Payment $payment): bool
    {
        return $payment->save();
    }

    public function create(array $data): Payment
    {
        $payment = new Payment();
        $payment->load($data, '');

        if (!$this->save($payment)) {
            throw new \RuntimeException('Failed to create payment: ' . implode(', ', $payment->getFirstErrors()));
        }

        return $payment;
    }
}