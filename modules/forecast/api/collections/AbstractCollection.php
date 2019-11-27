<?php

namespace app\modules\forecast\api\collections;

class AbstractCollection implements \Iterator, \ArrayAccess, \Countable
{
    private const INITIAL_POSITION = 0;
    private $elements = [];
    private $position = self::INITIAL_POSITION;

    /**
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->elements[$this->position];
    }

    /**
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @see Iterator::valid()
     */
    public function valid()
    {
        return isset($this->elements[$this->position]);
    }

    /**
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = self::INITIAL_POSITION;
    }

    /**
     * @see ArrayAccess::offsetExists()
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset] ?? null;
    }

    /**
     * @see ArrayAccess::offsetSet()
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    /**
     * @see ArrayAccess::offsetUnset()
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    /**
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->elements);
    }
}