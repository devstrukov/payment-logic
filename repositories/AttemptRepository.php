<?php
namespace app\repositories;

use app\models\Attempt;
use app\models\Payment;

class AttemptRepository implements AttemptRepositoryInterface
{
    public function createForPayment(Payment $payment, array $data): Attempt
    {
        $attempt = new Attempt();
        $attempt->payment_id = $payment->id;
        $attempt->load($data, '');

        if (!$this->save($attempt)) {
            throw new \RuntimeException('Failed to create attempt: ' . implode(', ', $attempt->getFirstErrors()));
        }

        return $attempt;
    }

    public function save(Attempt $attempt): bool
    {
        return $attempt->save();
    }
}