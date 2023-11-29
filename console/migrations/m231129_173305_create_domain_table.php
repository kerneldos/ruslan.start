<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%domain}}`.
 */
class m231129_173305_create_domain_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%domain}}', [
            'id' => $this->primaryKey(),
            'temp_name' => $this->string(6)->unique()->notNull(),
            'name' => $this->string(100)->unique()->null(),
            'user_id' => $this->integer(11)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%domain}}');
    }
}
