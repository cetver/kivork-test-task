<?php

namespace app\modules\forecast\api\streams;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class XmlStream implements StreamInterface
{
    use StreamDecoratorTrait;

    public function document()
    {
        $document = new \DOMDocument();
        $document->loadXML($this->getContents());

        return $document;
    }
}