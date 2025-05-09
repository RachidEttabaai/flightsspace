<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ServerException;
use Kevinrob\GuzzleCache\CacheMiddleware;

class MixRepository
{
    /**
     * UAPI's url
     *
     * @var string|null
     */
    private ?string $url;

    /**
     * Client object from GuzzleHttp
     *
     * @var Client
     */
    private Client $client;

    /**
     * HandlerStack object who creates a composed Guzzle handler function by
     * stacking middlewares on top of an HTTP handler function.s
     *
     * @var HandlerStack
     */
    private HandlerStack $handlerstack;

    /**
     * Class constructor
     */
    public function __construct(string $url = null)
    {
        $this->url = $url;
        $this->handlerstack = HandlerStack::create();
        $this->handlerstack->push(new CacheMiddleware(), "cache");
        $this->client = new Client(["handler" => $this->handlerstack]);
    }

    /**
     * Get url of the API
     *
     * @return  string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set url of the API
     *
     * @param  string|null  $url  Url of the API
     *
     * @return  self
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get client object from GuzzleHttp
     *
     * @return  Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Sending an HTTP request for getting data from an API
     *
     * @param Request $request
     * @return array
     */
    private function sendRequest(Request $request): array
    {
        $res = [];

        $client = $this->getClient();

        try {

            $promise = $client->sendAsync($request)->then(
                function ($response) {
                    $tab = json_decode($response->getBody()->getContents(), true);
                    //dd($tab["results"]);
                    return $tab["results"];
                }
            );

            $res = $promise->wait();
        } catch (ServerException $e) {

            $res = ["message" => $e->getMessage()];
        }

        return $res;
    }

    /**
     * Doing an api request and get datas from the api
     *
     * @return array
     */
    public function apirequest(): array
    {

        $apiurl = $this->getUrl();

        $res = [];

        if (!is_null($apiurl)) {

            $request = new Request("GET", $apiurl);
            $res = $this->sendRequest($request);
        } else {

            $res = ["message" => "Problem with the request for gettind data from the API url"];
        }

        return $res;
    }
}
