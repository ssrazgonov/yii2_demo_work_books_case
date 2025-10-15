<?php

use yii\db\Migration;

/**
 * Handles the creation of table `book_author`.
 */
class m251015_000030_create_book_authors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-book_author', '{{%book_author}}', ['book_id', 'author_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('pk-book_author', '{{%book_author}}');

        $this->dropTable('{{%book_author}}');
    }
}