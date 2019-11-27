<?php

namespace app\modules\forecast\models\search;

use yii\base\Model;
use yii\data\ArrayDataProvider;

class Forecast extends Model
{
    public $start;
    public $end;

    public function init()
    {
        $format = 'd.m.Y';
        $this->start = date($format, strtotime('-1 day'));
        $this->end = date($format);
    }

    public function rules()
    {
        return [
            [['start', 'end',], 'safe'],
        ];
    }

    public function search($params)
    {
        $this->load($params);

        $format = 'Y-m-d H:i:s';
        $sql = '
            SELECT
                MAX(forecast.temperature) max_temp,
                MIN(forecast.temperature) min_temp,
                AVG(forecast.temperature) avg_temp,
                country.name country_name,
                city.name city_name
            FROM country
            INNER JOIN city
            ON city.country_id = country.id
            INNER JOIN forecast
            ON forecast.city_id = city.id
            WHERE forecast.timestamp BETWEEN :s AND :e
            GROUP BY
                country.name,
                city.name
        ';
        $params = [
            ':s' => strtotime(date($format, strtotime("{$this->start} 00:00:00"))),
            ':e' => strtotime(date($format, strtotime("{$this->end} 23:59:59"))),
        ];
        $rows = \Yii::$app->getDb()->createCommand($sql, $params)->queryAll();

        return new ArrayDataProvider([
            'allModels' => $rows,
            'key' => 'country_name',
            'pagination' => [
                'pageSize' => 7,
            ],
            'sort' => [
                'defaultOrder' => [
                    'country_name' => SORT_ASC,
                    'city_name' => SORT_ASC,
                ],
                'attributes' => [
                    'country_name', 'city_name', 'max_temp', 'min_temp', 'avg_temp',
                ],
            ],
        ]);
    }
}
