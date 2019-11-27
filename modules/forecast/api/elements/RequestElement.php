<?php

namespace app\modules\forecast\api\elements;

class RequestElement
{
    /**
     * @var string
     */
    private $city;
    /**
     * @var int
     */
    private $startAt;
    /**
     * @var int
     */
    private $endAt;

    public function __construct(string $city, int $startAt, int $endAt)
    {
        $this->city = $city;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStartAt(): string
    {
        return date('d.m.Y', $this->startAt);
    }

    /**
     * @return string
     */
    public function getEndAt(): string
    {
        return date('d.m.Y', $this->endAt);
    }
}