<?php

namespace app\modules\forecast\models;

use Yii;

/**
 * This is the model class for table "forecast".
 *
 * @property int $id
 * @property int $city_id
 * @property float $temperature
 * @property string $timestamp
 *
 * @property City $city
 */
class Forecast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'forecast';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'temperature', 'timestamp'], 'required'],
            [['city_id'], 'default', 'value' => null],
            [['city_id'], 'integer'],
            [['temperature'], 'number'],
            [['timestamp'], 'safe'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'temperature' => 'Temperature',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * {@inheritdoc}
     * @return ForecastQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ForecastQuery(get_called_class());
    }
}
