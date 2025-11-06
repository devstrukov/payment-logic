<?php
namespace app\services;

use app\models\User;
use app\models\LoginForm;
use Yii;

class AuthService implements AuthServiceInterface
{
    public function login(string $email, string $password): array
    {
        Yii::info("Login attempt for email: {$email}");

        $model = new LoginForm();
        $model->email = $email;
        $model->password = $password;

        if ($model->login()) {
            $user = $model->getUser();
            Yii::info("Login successful for user: {$user->id}");

            return [
                'success' => true,
                'token' => $user->token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'balance' => $user->balance
                ]
            ];
        } else {
            Yii::error("Login failed for email: {$email}. Errors: " . json_encode($model->getErrors()));
            return [
                'success' => false,
                'errors' => $model->getErrors()
            ];
        }
    }

    public function getCurrentUser(): ?User
    {
        return Yii::$app->user->identity;
    }
}