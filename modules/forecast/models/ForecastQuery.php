<?php

namespace app\modules\forecast\models;

/**
 * This is the ActiveQuery class for [[Forecast]].
 *
 * @see Forecast
 */
class ForecastQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Forecast[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Forecast|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
