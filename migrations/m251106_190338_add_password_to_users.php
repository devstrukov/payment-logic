<?php
use yii\db\Migration;

class m251106_190338_add_password_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'password_hash', $this->string(255));

        // Устанавливаем пароль для существующих пользователей
        $users = \app\models\User::find()->all();
        foreach ($users as $user) {
            $user->setPassword('password123'); // временный пароль
            $user->save();
        }
    }

    public function safeDown()
    {
        $this->dropColumn('users', 'password_hash');
    }
}