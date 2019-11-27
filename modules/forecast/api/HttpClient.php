<?php

namespace app\modules\forecast\api;

use app\modules\forecast\api\collections\RequestCollection;
use app\modules\forecast\api\collections\ResponseCollection;
use app\modules\forecast\api\elements\ResponseElement;
use app\modules\forecast\api\streams\XmlStream;
use app\modules\forecast\iterators\ChunkedIterator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use \yii\log\Logger;
use function GuzzleHttp\Promise\settle;
use function GuzzleHttp\Psr7\str;

class HttpClient extends Client
{
    /**
     * @var int
     */
    private $concurrentReqNum;
    /**
     * @var int
     */
    private $connectionRetries;
    /**
     * @var int
     */
    private $connectionRetriesInterval;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var HandlerStack
     */
    private $handlerStack;

    public function __construct(
        $concurrentReqNum = 5,
        $connectionRetries = 3,
        $connectionRetriesInterval = 1500,
        Logger $logger = null,
        HandlerStack $handlerStack = null
    )
    {
        $this->concurrentReqNum = $concurrentReqNum;
        $this->connectionRetries = $connectionRetries;
        $this->connectionRetriesInterval = $connectionRetriesInterval;
        $this->handlerStack = $handlerStack;
        $this->logger = $logger;

        $this->createHandlerStack();

        $config = [
            'base_uri' => 'http://quiz.dev.travelinsides.com',
            'handler' => $this->handlerStack,
            'decode_content' => true,
            'allow_redirects' => false,
            'headers' => [
                'Accept' => 'application/xml',
                'Accept-Encoding' => 'gzip, deflate',
            ],
        ];

        parent::__construct($config);
        $this->concurrentReqNum = $concurrentReqNum;
    }

    /**
     * @param RequestCollection $requestCollection
     *
     * @return ResponseCollection
     */
    public function getForecast(RequestCollection $requestCollection)
    {
        $responseCollection = new ResponseCollection();
        foreach (new ChunkedIterator($requestCollection, $this->concurrentReqNum) as $items) {
            $promises = [];
            /** @var \app\modules\forecast\api\elements\RequestElement $item */
            foreach ($items as $item) {
                $promises[] = $this->getAsync('/forecast/api/getForecast', [
                    'query' => [
                        'city' => $item->getCity(),
                        'start' => $item->getStartAt(),
                        'end' => $item->getEndAt(),
                    ],
                ]);
            }

            /** @var \DOMDocument[] $documents */
            $documents = $this->runRequests($promises);
            foreach ($documents as $document) {
                $rowsLength = $document->getElementsByTagName('row')->length;
                $xpath = new \DOMXPath($document);
                for ($i = 1; $i < $rowsLength; $i++) {
                    $responseCollection[] = new ResponseElement(
                        $xpath->query("//rows/row[$i]/city")->item(0)->nodeValue,
                        $xpath->query("//rows/row[$i]/temperature")->item(0)->nodeValue,
                        $xpath->query("//rows/row[$i]/ts")->item(0)->nodeValue
                    );
                }
            }
        }

        return $responseCollection;
    }

    /**
     * @param PromiseInterface[] $promises
     */
    private function runRequests(array $promises)
    {
        /**
         * @var $response Response
         * @var $xmlStream XmlStream
         */
        $documents = [];
        $prms = settle($promises)->wait();
        foreach ($prms as $prm) {
            switch ($prm['state']) {
                default:
                    throw new \RuntimeException('Invalid state');
                case PromiseInterface::REJECTED:
                    throw $prm['reason'];
                    break;
                case PromiseInterface::FULFILLED:
                    $response = $prm['value'];
                    $xmlStream = $response->getBody();
                    $document = $xmlStream->document();
                    $this->validateResponseErrors($document);
                    $documents[] = $document;
            }
        }

        return $documents;
    }

    private function validateResponseErrors(\DOMDocument $document)
    {
        $errors = [];
        $xpath = new \DOMXPath($document);
        /** @var \DOMNode $error */
        foreach ($xpath->query('///errors/*') as $error) {
            $errors[] = $error->nodeValue;
        }

        if (!empty($errors)) {
            throw new \RuntimeException(print_r($errors, true));
        }
    }

    private function createHandlerStack()
    {
        $retryMiddleware = $this->createRetryMiddleware($this->connectionRetries, $this->connectionRetriesInterval);

        if ($this->handlerStack === null) {
            $this->handlerStack = new HandlerStack(new CurlHandler());
        }
        $this->handlerStack->remove('http_errors');
        $this->handlerStack->remove('prepare_body');
        $this->handlerStack->remove('connection_retries');

        $this->handlerStack->push(Middleware::httpErrors(), 'http_errors');
        $this->handlerStack->push(Middleware::prepareBody(), 'prepare_body');
        $this->handlerStack->push(Middleware::retry($retryMiddleware['decider'], $retryMiddleware['delay']), 'connection_retries');
        $this->handlerStack->push(
            Middleware::mapResponse(function (Response $response) {
                $xmlStream = new XmlStream($response->getBody());

                return $response->withBody($xmlStream);
            }),
            'response_json'
        );
    }

    private function createRetryMiddleware($connectionRetries, $connectionRetriesInterval)
    {
        $decider = function (
            $connectionRetry,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) use ($connectionRetries) {
            $connectionRetry++;

            $message = [
                sprintf('Trying to establish a connection, retry %d of %d...', $connectionRetry, $connectionRetries),
                '***** REQUEST *****',
                str($request),
            ];
            if ($response !== null) {
                $responseBody = $response->getBody();
                $offset = $responseBody->tell();
                $message[] = '';
                $message[] = '***** RESPONSE *****';
                $message[] = str($response);
                $responseBody->seek($offset);
            }

            $this->log($message);

            $isBadHttpCode = ($response !== null && $response->getStatusCode() !== 200);
            if ($connectionRetry >= $connectionRetries) {
                $this->log('Unsuccessful');
                if ($isBadHttpCode === true) {
                    throw new RequestException('Wrong status code', $request, $response, $exception);
                }

                return false;
            }
            if ($exception !== null || $isBadHttpCode === true) {
                $this->log('Unsuccessful');

                return true;
            }

            $this->log('Successful');

            return false;
        };

        $delay = function () use ($connectionRetriesInterval) {
            $this->log(sprintf(
                'Fell asleep for %d milliseconds, using connection retries interval',
                $connectionRetriesInterval
            ));

            return $connectionRetriesInterval;
        };

        return compact('decider', 'delay');
    }

    private function log($message)
    {
        if ($this->logger !== null) {
            $this->logger->log($message, $this->logger::LEVEL_TRACE, __CLASS__);
        }
    }
}