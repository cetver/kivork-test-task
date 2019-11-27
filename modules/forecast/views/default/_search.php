<?php

/* @var $this yii\web\View */
/* @var $model app\modules\forecast\models\search\Forecast */
/* @var $form yii\widgets\ActiveForm */

use app\modules\forecast\assets\DateTimePickerAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// должен быть в виджете полей start/end
$dtp = <<<JS
$('.dtp').datetimepicker({
    format: 'DD.MM.YYYY',
    ignoreReadonly: true
}); 
JS;

$this->registerAssetBundle(DateTimePickerAsset::class);
$this->registerJs($dtp);
?>

<div class="forecast-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'id' => 'search-form',
            'data-pjax' => 1
        ],
    ]);
    ?>

    <div class="col-xs-2">
        <?php
        // тут должен быть виджет
        echo $form->field($model, 'start', [
            'template' => '
                {label}
                <div class="input-group date dtp">
                    {input}
                    <span class="input-group-addon">
                        <span class="glyphicon-calendar glyphicon"></span>
                    </span>
                </div>
                {hint}
                {error}
            ',
            'inputOptions' => [
                'readonly' => 'readonly',
                'class' => 'form-control'
            ]
        ]);
        ?>
    </div>

    <div class="col-xs-2">
        <?php
        // тут должен быть виджет
        echo $form->field($model, 'end', [
            'template' => '
                {label}
                <div class="input-group dtp">
                    {input}
                    <span class="input-group-addon">
                        <span class="glyphicon-calendar glyphicon"></span>
                    </span>
                </div>
                {hint}
                {error}
            ',
            'inputOptions' => [
                'readonly' => 'readonly',
                'class' => 'form-control'
            ]
        ]);
        ?>
    </div>

    <div class="col-xs-2">
        <div class="form-group">
            <?php
            // тут должен быть виджет
            $htmlOptions = array(
                'type' => 'submit',
                'class' => 'btn btn-success',
                'style' => 'margin-top: 25px'
            );
            $content = sprintf('<span class="glyphicon glyphicon-search"></span> %s', 'Search');
            echo Html::button($content,  $htmlOptions);
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
