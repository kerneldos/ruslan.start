<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stage_storage}}`.
 */
class m230705_231122_create_stage_storage_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stage_storage}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(255)->notNull(),
            'value' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%stage_storage}}');
    }
}
