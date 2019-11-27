<?php

use app\modules\forecast\grid\ActionsColumn;
use app\modules\forecast\grid\DegreeColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\forecast\models\search\Forecast */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Statistics',];

?>

<div class="panel panel-default">
    <div class="panel-heading">Search</div>
    <div class="panel-body row">
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
</div>

<?php
Pjax::begin([
    'options' => ['class' => 'panel panel-default'],
    'formSelector' => '#search-form'
]);
?>
<div class="panel-body">
    <!--тут должен быть кастомный GridView-->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '
            <div class="row">
                <div class="col-md-6" style="margin-top: 26px;">
                    <div class="text-muted">{summary}</div>
                </div>
                <div class="col-md-6 text-right">
                    {pager}
                </div>
            </div>
            <div class="table-responsive">
                {items}
            </div>
        ',
        'columns' => [
            'country_name',
            'city_name',
            [
                'class' => DegreeColumn::class,
                'attribute'=> 'max_temp',
                'label' => 'Max temperature',
            ],
            [
                'class' => DegreeColumn::class,
                'attribute'=> 'min_temp',
                'label' => 'Min temperature',
            ],
            [
                'attribute'=> 'avg_temp',
                'class' => DegreeColumn::class,
                'label' => 'Avg temperature',
            ],
            [
                'class' => ActionsColumn::class,
            ]
        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

