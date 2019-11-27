<?php

namespace app\modules\forecast\grid;

use app\modules\forecast\converters\DegreeConverterInterface;
use yii\grid\DataColumn;

class DegreeColumn extends DataColumn
{
    /**
     * @var DegreeConverterInterface
     */
    private $converter;

    public function __construct(DegreeConverterInterface $converter, $config = [])
    {
        $this->converter = $converter;
        parent::__construct($config);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->converter->convert(parent::renderDataCellContent($model, $key, $index));

        return $value . '&nbsp;&#8451;';
    }
}