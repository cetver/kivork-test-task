<?php

namespace app\modules\forecast\assets;

use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
    public $baseUrl = '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0';
    public $js = ['moment.min.js'];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}