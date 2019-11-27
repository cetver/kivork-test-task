<?php

/**
 * @var yii\web\View $this
 * @var string $cityName
 * @var string $countryName
 * @var array $rows
 * @var int $countRows
 * @var int $i
 */

use yii\helpers\Html;

$this->title = sprintf('History of %s (%s)', $cityName, $countryName);
$this->params['breadcrumbs'][] = [
    'label' => 'Statistics',
    'url' => ['/forecast/default/index'],
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="panel-body">
        <?php if ($countRows === 0): ?>
            No results found.
        <?php else: ?>
            <div class="row">
            <?php foreach ($rows as $day => $data): ?>
                <?php $i++; ?>
                <div class="col-xs-3">
                    <p>
                        <strong><?= Html::encode($day) ?></strong>
                    </p>
                    <?php foreach ($data as $hour => $val): ?>
                        <p>
                            <?php printf('%s %s', $hour, $val['temperature']) ?> &nbsp;&#8451;
                        </p>
                    <?php endforeach; ?>

                </div>
                <?php if ($i % 4 == 0 && $i < $countRows): ?>
                    </div><div class="row">
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
