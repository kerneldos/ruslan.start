<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%config}}`.
 */
class m230608_093553_create_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%config}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'name' => $this->string()->notNull()->unique(),
            'value' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%config}}');
    }
}
