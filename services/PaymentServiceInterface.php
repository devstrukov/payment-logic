<?php
namespace app\services;

interface PaymentServiceInterface
{
    public function createPayment(array $data): array;
    public function getPaymentStatus(int $paymentId): array;
}