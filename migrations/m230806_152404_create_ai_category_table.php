<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ai_category}}`.
 */
class m230806_152404_create_ai_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ai_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ai_category}}');
    }
}
