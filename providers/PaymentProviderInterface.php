<?php
namespace app\providers;

interface PaymentProviderInterface
{
    public function getIntegrationId(): int;
    public function getBalance(): float;
    public function processPayment(array $data): array;
}