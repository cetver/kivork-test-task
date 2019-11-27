<?php

namespace app\modules\forecast\controllers;

use app\modules\forecast\converters\DegreeConverterInterface;
use app\modules\forecast\models\City;
use Yii;
use app\modules\forecast\models\search\Forecast as ForecastSearch;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * DefaultController implements the CRUD actions for Forecast model.
 */
class DefaultController extends Controller
{
    /**
     * @var DegreeConverterInterface
     */
    private $degreeConverter;

    public function __construct($id, $module, DegreeConverterInterface $degreeConverter, $config = [])
    {
        $this->degreeConverter = $degreeConverter;
        parent::__construct($id, $module, $config);
    }

    public function actionHistory($city)
    {
        $city = str_replace('_', ' ', $city);
        $model = City::findOne(['name' => $city]);
        if ($model === null) {
            throw new HttpException(400, 'City does not exists');
        } else {
            $cityName = $model->name;
            $countryName = $model->country->name;
            $rows = [];
            foreach ($model->forecasts as $forecast) {
                $createdAt = Yii::$app->getFormatter()->asDate($forecast->timestamp, 'long');
                $hour = date('H:i:s', $forecast->timestamp);
                $rows[$createdAt][$hour] = [
                    'temperature' => $this->degreeConverter->convert($forecast->temperature)
                ];
            }

            $countRows = count($rows);
            $i = 0;

            return $this->render('history', compact(
                'cityName',
                'countryName',
                'rows',
                'countRows',
                'i'
            ));
        }
    }

    /**
     * Lists all Forecast models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ForecastSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
