<?php

namespace app\modules\forecast;

use app\modules\forecast\api\HttpClient;
use app\modules\forecast\commands\ForecastController;
use app\modules\forecast\controllers\DefaultController;
use app\modules\forecast\converters\FahrenheitToCelsiusDegreeConverter;
use app\modules\forecast\grid\DegreeColumn;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\di\Container;

/**
 * forecast module definition class
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\forecast\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $this->buildDependencyInjection();
        $this->buildRoutes($app);
    }

    private function buildDependencyInjection()
    {
        \Yii::$container
            ->setSingleton(ForecastController::class, function (Container $container, $params, $config) {
                return new ForecastController(
                    $params[0],
                    $params[1],
                    new HttpClient(5, 3, 1500, \Yii::getLogger()),
                    $config
                );
            })
            ->setSingleton(DefaultController::class, function (Container $container, $params, $config) {
                return new DefaultController(
                    $params[0],
                    $params[1],
                    new FahrenheitToCelsiusDegreeConverter(),
                    $config
                );
            })
            ->setSingleton(DegreeColumn::class, function (Container $container, $params, $config) {
                return new DegreeColumn(
                    new FahrenheitToCelsiusDegreeConverter(),
                    $config
                );
            });
    }

    private function buildRoutes(Application $app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules(
                [
                    'forecast/default/history/<city:\w+>' => 'forecast/default/history',
                ],
                false
            );
        }
    }
}
