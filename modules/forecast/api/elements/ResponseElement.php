<?php

namespace app\modules\forecast\api\elements;

class ResponseElement
{
    private $city;
    private $temperature;
    private $ts;

    public function __construct(string $city, float $temperature, int $ts)
    {
        $this->city = $city;
        $this->temperature = $temperature;
        $this->ts = $ts;
    }

    /**
     * @return string
     */
    public function getCity():string
    {
        return $this->city;
    }

    /**
     * @return float
     */
    public function getTemperature():float
    {
        return $this->temperature;
    }

    /**
     * @return int
     */
    public function getTs():int
    {
        return $this->ts;
    }
}

