<?php

namespace app\modules\forecast\api\collections;;

use app\modules\forecast\api\elements\ResponseElement;

class ResponseCollection extends AbstractCollection
{
    public function current(): ResponseElement
    {
        return parent::current();
    }

    public function offsetGet($offset): ResponseElement
    {
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof ResponseElement) {
            throw new \InvalidArgumentException(
                sprintf('The "%s" value is not an instance of "%s"', $value, ResponseElement::class)
            );
        }

        parent::offsetSet($offset, $value);
    }
}