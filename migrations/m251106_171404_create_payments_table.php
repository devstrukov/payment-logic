<?php
use yii\db\Migration;

class m251106_171404_create_payments_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('payments', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(15, 2)->notNull(),
            'status' => $this->string(50)->notNull()->defaultValue('pending'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-payments-user_id', 'payments', 'user_id');
        $this->createIndex('idx-payments-status', 'payments', 'status');
        $this->createIndex('idx-payments-created_at', 'payments', 'created_at');

        $this->addForeignKey(
            'fk-payments-user_id',
            'payments',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-payments-user_id', 'payments');
        $this->dropTable('payments');
    }
}