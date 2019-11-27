<?php

namespace app\modules\forecast\converters;

class FahrenheitToCelsiusDegreeConverter implements DegreeConverterInterface
{
    public function convert($value)
    {
        return (($value - 32) * 5) / 9;
    }
}