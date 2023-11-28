<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hash_column_ai_text_category}}`.
 */
class m231014_092420_create_parent_id_column_ai_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%ai_category}}', 'parent_id', $this->integer(11)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
