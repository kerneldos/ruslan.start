<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%portal}}`.
 */
class m231203_095133_create_portal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%portal}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null()->defaultValue(null),
            'temp_name' => $this->string(6)->unique()->notNull(),
            'name' => $this->string(100)->unique()->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%portal}}');
    }
}
