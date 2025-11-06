<?php
namespace app\services;

interface AuthServiceInterface
{
    public function login(string $email, string $password): array;
    public function getCurrentUser(): ?\app\models\User;
}