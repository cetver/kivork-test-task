<?php

namespace app\modules\forecast\assets;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public $baseUrl = '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47';
    public $css = ['css/bootstrap-datetimepicker.min.css'];
    public $js = ['js/bootstrap-datetimepicker.min.js'];
    public $depends = [
        'app\modules\forecast\assets\MomentAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}