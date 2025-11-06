<?php
namespace app\services;

interface UserServiceInterface
{
    public function getCurrentUserBalance(): float;
    public function getUserById(int $userId): ?\app\models\User;
}