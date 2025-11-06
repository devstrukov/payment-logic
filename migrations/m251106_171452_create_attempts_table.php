<?php
use yii\db\Migration;

class m251106_171452_create_attempts_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('attempts', [
            'id' => $this->primaryKey(),
            'payment_id' => $this->integer()->notNull(),
            'integration_id' => $this->integer()->notNull(),
            'status' => $this->string(50)->notNull(),
            'external_id' => $this->string(255),
            'error_message' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-attempts-payment_id', 'attempts', 'payment_id');
        $this->createIndex('idx-attempts-integration_id', 'attempts', 'integration_id');
        $this->createIndex('idx-attempts-status', 'attempts', 'status');
        $this->createIndex('idx-attempts-external_id', 'attempts', 'external_id');

        $this->addForeignKey(
            'fk-attempts-payment_id',
            'attempts',
            'payment_id',
            'payments',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-attempts-integration_id',
            'attempts',
            'integration_id',
            'integrations',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-attempts-payment_id', 'attempts');
        $this->dropForeignKey('fk-attempts-integration_id', 'attempts');
        $this->dropTable('attempts');
    }
}