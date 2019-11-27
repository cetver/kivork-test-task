<?php

namespace app\modules\forecast\grid;

use yii\grid\Column;
use yii\helpers\Html;

class ActionsColumn extends Column
{
    public $header = 'Actions';

    protected function renderDataCellContent($model, $key, $index)
    {
        $city = str_replace(' ', '_', $model['city_name']);
        $historyLink =  Html::a('History', [
            '/forecast/default/history',
            'city' => $city
        ]);
        $buttonId = "col-action-dropdown-$index";

        return <<<HTML
<div class="dropdown">
    <button class="btn btn-default dropdown-toggle" type="button" id="$buttonId" data-toggle="dropdown">
        Action
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="$buttonId">
        <li role="presentation">
            $historyLink
        </li>
    </ul>
</div>
HTML;
    }
}