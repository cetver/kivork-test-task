<?php

use yii\db\Migration;

/**
 * Class m191124_142134_fill_country_table
 */
class m191124_142134_fill_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('{{%country}}', ['name'], $this->getRows());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%country}}', [
            'name' => array_column($this->getRows(), 'name')
        ]);
    }

    private function getRows()
    {
        return [
            ['name' => 'Russia',],
            ['name' => 'USA',],
        ];
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191124_142134_fill_country_table cannot be reverted.\n";

        return false;
    }
    */
}
