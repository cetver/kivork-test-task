<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%forecast}}`.
 */
class m191124_145923_create_forecast_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%forecast}}', [
            'id' => $this->primaryKey(),
            'city_id' => $this->integer()->notNull(),
            'temperature' => $this->float()->notNull(),
            'timestamp' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'forecast_city_id_fkey',
            '{{%forecast}}',
            'city_id',
            '{{%city}}',
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
        $this->dropTable('{{%forecast}}');
    }
}
