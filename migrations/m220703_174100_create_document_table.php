<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%document}}`.
 */
class m220703_174100_create_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document}}', [
            'id' => $this->string(255),
            'status' => $this->integer()->notNull(),
            'payload' => $this->text()->defaultValue('{}'),
            'createAt' => $this->timestamp()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP()')),
            'modifyAt' => $this->timestamp()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP()')),
            'PRIMARY KEY(id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document}}');
    }
}
