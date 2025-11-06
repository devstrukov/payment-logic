<?php
use yii\db\Migration;

class m251106_171321_create_integrations_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('integrations', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-integrations-title', 'integrations', 'title');
        $this->createIndex('idx-integrations-is_active', 'integrations', 'is_active');
    }

    public function safeDown()
    {
        $this->dropTable('integrations');
    }
}