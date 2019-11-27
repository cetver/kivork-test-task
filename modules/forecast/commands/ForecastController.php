<?php

namespace app\modules\forecast\commands;

use app\modules\forecast\api\collections\RequestCollection;
use app\modules\forecast\api\collections\ResponseCollection;
use app\modules\forecast\api\elements\RequestElement;
use app\modules\forecast\api\HttpClient;
use app\modules\forecast\iterators\ChunkedIterator;
use app\modules\forecast\models\City;
use yii\console\Controller;
use yii\console\ExitCode;

class ForecastController extends Controller
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct($id, $module, HttpClient $httpClient, $config = [])
    {
        $this->httpClient = $httpClient;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex($start = '01.11.2019', $end = '30.11.2019')
    {
        $requestCollection = new RequestCollection();
        $cities = City::find()->all();
        $citiesMap = [];
        foreach ($cities as $city) {
            $requestCollection[] = new RequestElement($city->name, strtotime($start), strtotime($end));
            $citiesMap[$city->name] = $city->id;
        }

        $responseCollection = $this->httpClient->getForecast($requestCollection);
        $rowCount = $this->batchInsert($responseCollection, $citiesMap);

        echo "$rowCount\n";

        return ExitCode::OK;
    }

    private function batchInsert(ResponseCollection $collection, array $citiesMap)
    {
        // этот код нужно было вынести в хелпер в универсальный метод, но мне лень :)
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            \Yii::$app
                ->getDb()
                ->createCommand("                                
                    CREATE TABLE tmp_forecast (                
                        city_id INTEGER NOT NULL,                  
                        temperature DOUBLE PRECISION NOT NULL,
                        timestamp INTEGER NOT NULL
                    );        
                ")
                ->execute();
            /** @var \app\modules\forecast\api\elements\ResponseElement[] $elements */
            // да, да магическое число
            foreach (new ChunkedIterator($collection, 999) as $elements) {
                $i = 0; // SQLSTATE[HY093]: Invalid parameter number: Columns/Parameters are 1-based
                $sql = "INSERT INTO tmp_forecast(city_id, temperature, timestamp) VALUES ";
                $params = [];
                foreach ($elements as $element) {
                    $sql .= '(?,?,?),';
                    $params[++$i] = $citiesMap[$element->getCity()];
                    $params[++$i] = $element->getTemperature();
                    $params[++$i] = $element->getTs();
                }
                $sql = rtrim($sql, ',');
                \Yii::$app->getDb()->createCommand($sql, $params)->execute();
            }

            $rowCount = \Yii::$app
                ->getDb()
                ->createCommand('
                    INSERT INTO forecast (city_id, temperature, timestamp)
                    SELECT
                        tmp_forecast.city_id,
                        tmp_forecast.temperature,
                        tmp_forecast.timestamp
                    FROM tmp_forecast
                    LEFT JOIN forecast
                    ON  forecast.city_id   = tmp_forecast.city_id
                    AND forecast.timestamp = tmp_forecast.timestamp
                    WHERE forecast.id IS NULL
                ')
                ->execute();
            \Yii::$app->getDb()->createCommand('DROP TABLE tmp_forecast;')->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $rowCount;
    }
}
