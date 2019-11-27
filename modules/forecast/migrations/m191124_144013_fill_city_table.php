<?php

use yii\db\Migration;

/**
 * Class m191124_144013_fill_city_table
 */
class m191124_144013_fill_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $russiaId = $this->getRussiaId();
        $usaId = $this->getUsaId();
        $this->batchInsert('{{%city}}', ['country_id', 'name'], [
            ['country_id' => $russiaId, 'name' => 'Moscow'],
            ['country_id' => $russiaId, 'name' => 'Nizhny Novgorod'],
            ['country_id' => $russiaId, 'name' => 'Novosibirsk'],
            ['country_id' => $russiaId, 'name' => 'Saint Petersburg'],
            ['country_id' => $russiaId, 'name' => 'Yekaterinburg'],
            ['country_id' => $usaId, 'name' => 'Chicago'],
            ['country_id' => $usaId, 'name' => 'Houston'],
            ['country_id' => $usaId, 'name' => 'Los Angeles'],
            ['country_id' => $usaId, 'name' => 'New York'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%city}}', [
            'country_id' => $this->getRussiaId(),
            'name' => ['Moscow', 'Nizhny Novgorod', 'Novosibirsk', 'Saint Petersburg', 'Yekaterinburg']
        ]);
        $this->delete('{{%city}}', [
            'country_id' => $this->getUsaId(),
            'name' => ['Chicago', 'Houston', 'Los Angeles', 'New York']
        ]);
    }

    private function getRussiaId()
    {
        return $this->getDb()->createCommand('SELECT id FROM country WHERE name = :name', [':name' => 'Russia'])->queryScalar();
    }

    private function getUsaId()
    {
        return $this->getDb()->createCommand('SELECT id FROM country WHERE name = :name', [':name' => 'USA'])->queryScalar();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191124_144013_fill_city_table cannot be reverted.\n";

        return false;
    }
    */
}
