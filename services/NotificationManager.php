<?php
namespace app\services;

use Yii;

class NotificationManager
{
    private $services = [];

    public function addService(NotificationServiceInterface $service): void
    {
        $this->services[] = $service;
    }

    public function notifyAll(string $message, string $recipient, string $type = null): void
    {
        foreach ($this->services as $service) {
            if ($type === null || $service->canHandle($type)) {
                try {
                    $service->notify($message, $recipient);
                } catch (\Exception $e) {
                    Yii::error("Notification failed: " . $e->getMessage());
                }
            }
        }
    }

    public function getAvailableTypes(): array
    {
        $types = [];
        foreach ($this->services as $service) {
            if ($service instanceof NotificationServiceInterface) {
                // Определяем тип по имени класса
                $className = get_class($service);
                if (strpos($className, 'Email') !== false) {
                    $types[] = 'email';
                } elseif (strpos($className, 'Sms') !== false) {
                    $types[] = 'sms';
                }
            }
        }
        return array_unique($types);
    }
}