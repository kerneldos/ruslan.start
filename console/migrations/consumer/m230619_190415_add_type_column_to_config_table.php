<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%config}}`.
 */
class m230619_190415_add_type_column_to_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%config}}', 'type', $this->string()->defaultValue('text'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%config}}', 'type');
    }
}
