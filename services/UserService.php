<?php
namespace app\services;

use app\models\User;

class UserService implements UserServiceInterface
{
    public function getCurrentUserBalance(): float
    {
        // TODO: В коммите 4 заменим на реального пользователя из токена
        $user = $this->getCurrentUser();
        return $user->balance;
    }

    public function getUserById(int $userId): ?User
    {
        return User::findOne($userId);
    }

    private function getCurrentUser(): User
    {
        // TODO: В коммите 4 заменим на реального пользователя из токена
        // Сейчас возвращаем тестового пользователя
        $user = User::findOne(1);
        if (!$user) {
            throw new \RuntimeException('Current user not found');
        }
        return $user;
    }

    public function validateUserHasSufficientBalance(float $amount): bool
    {
        $currentBalance = $this->getCurrentUserBalance();
        return $currentBalance >= $amount;
    }

    public function getCurrentUserId(): int
    {
        return $this->getCurrentUser()->id;
    }
}