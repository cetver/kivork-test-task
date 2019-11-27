<?php

namespace app\modules\forecast\api\collections;

use app\modules\forecast\api\elements\RequestElement;

class RequestCollection extends AbstractCollection
{
    public function current(): RequestElement
    {
        return parent::current();
    }

    public function offsetGet($offset): RequestElement
    {
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof RequestElement) {
            throw new \InvalidArgumentException(
                sprintf('The "%s" value is not an instance of "%s"', $value, RequestElement::class)
            );
        }

        parent::offsetSet($offset, $value);
    }
}