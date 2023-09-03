<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hash_column_ai_text_category}}`.
 */
class m230903_092420_create_hash_column_ai_text_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%ai_text_category}}', 'hash', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
