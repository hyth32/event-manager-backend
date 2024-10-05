<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%access_tokens}}`.
 */
class m241005_103917_create_access_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%accessTokens}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'token' => $this->string()->notNull(),
            'expiresAt' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-accessTokens-userId',
            '{{%accessTokens}}',
            'userId',
        );

        $this->addForeignKey(
            'fk-accessTokens-userId',
            '{{%accessTokens}}',
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
        $this->dropIndex('idx-accessTokens-userId', '{{%accessTokens}}');
        $this->dropForeignKey('fk-accessTokens-userId', '{{%accessTokens}}');

        $this->dropTable('{{%access_tokens}}');
    }
}
