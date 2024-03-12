<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue_index}}`.
 */
class m240114_095242_create_queue_index_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%queue_index}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(50),
            'data' => $this->text(),
            'md5' => $this->string(32),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue_index}}');
    }
}
