<?php

use yii\db\Migration;

/**
 * Class m230902_164219_create_ai_text_category
 */
class m230902_164219_create_ai_text_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ai_text_category}}', [
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
        echo "m230902_164219_create_ai_text_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230902_164219_create_ai_text_category cannot be reverted.\n";

        return false;
    }
    */
}
