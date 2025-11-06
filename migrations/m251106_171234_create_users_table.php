<?php
use yii\db\Migration;

class m251106_171234_create_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull()->unique(),
            'token' => $this->string(255)->notNull(),
            'balance' => $this->decimal(15, 2)->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-users-email', 'users', 'email');
        $this->createIndex('idx-users-token', 'users', 'token');
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}