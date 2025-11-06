<?php
namespace app\services;

interface NotificationServiceInterface
{
    public function notify(string $message, string $recipient): bool;
    public function canHandle(string $type): bool;
}