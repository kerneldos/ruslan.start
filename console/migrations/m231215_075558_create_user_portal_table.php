<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_portal}}`.
 */
class m231215_075558_create_user_portal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_portal}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'portal_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk_user_id',
            '{{%user_portal}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_portal_id',
            '{{%user_portal}}',
            'portal_id',
            '{{%portal}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_portal}}');

        $this->dropForeignKey('fk_user_id', '{{%user}}');
        $this->dropForeignKey('fk_portal_id', '{{%portal}}');
    }
}
