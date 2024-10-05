<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%events}}`.
 */
class m241005_130628_create_events_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%events}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'name' => $this->string()->defaultValue('Event'),
            'content' => $this->text(),
            'startTime' => $this->integer(),
            'endTime' => $this->integer(),
            'createdAt' => $this->integer()->notNull(),
            'updatedAt' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-events-userId',
            '{{%events}}',
            'userId',
        );

        $this->addForeignKey(
            'fk-events-userId',
            '{{%events}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-events-userId', '{{%events}}');
        $this->dropForeignKey('fk-events-userId', '{{%events}}');

        $this->dropTable('{{%events}}');
    }
}
