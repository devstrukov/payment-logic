<?php
namespace app\services;

use app\models\Integration;

class IntegrationService
{
    public function getActiveIntegrations(): array
    {
        return Integration::find()
            ->where(['is_active' => true])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    public function getIntegrationById(int $id): ?Integration
    {
        return Integration::findOne(['id' => $id, 'is_active' => true]);
    }

    public function switchToIntegration(int $integrationId): bool
    {
        $integration = $this->getIntegrationById($integrationId);
        if (!$integration) {
            throw new \RuntimeException("Integration {$integrationId} not found or inactive");
        }

        // Здесь можно добавить логику смены провайдера в runtime
        // Пока просто возвращаем успех если интеграция существует и активна
        return true;
    }
}