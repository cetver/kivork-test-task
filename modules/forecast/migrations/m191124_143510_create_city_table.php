<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%city}}`.
 */
class m191124_143510_create_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%city}}', [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull(),
            'name' => $this->text()->notNull()->unique(),
        ]);
        $this->addForeignKey(
            'city_country_id_fkey',
            '{{%city}}',
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%city}}');
    }
}
