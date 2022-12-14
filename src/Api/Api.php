<?php

namespace Fungku\HubSpot\Api;

use Fungku\HubSpot\Contracts\HttpClient;
use Fungku\HubSpot\Support\QueryBuilder;

abstract class Api
{
    const USER_AGENT = 'Fungku_HubSpot_PHP/0.9 (https://github.com/ryanwinchester/hubspot-php)';

    /**
     * @var string
     */
    protected $baseUrl = "https://api.hubapi.com";

    /**
     * @var int
     */
    protected $urlEncoding = PHP_QUERY_RFC3986;

    /**
     * @var string
     */
    protected $privateAppToken;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var bool
     */
    private $oauth;

    /**
     * @param  string     $privateAppToken
     * @param  HttpClient $client
     * @param  bool       $oauth
     */
    public function __construct($privateAppToken, HttpClient $client, $oauth = false)
    {
        $this->privateAppToken = $privateAppToken;
        $this->client = $client;
        $this->oauth = $oauth;
    }

    /**
     * Send the request to the HubSpot API.
     *
     * @param  string $method  The HTTP request verb.
     * @param  string $url     The url to send the request to.
     * @param  array  $options An array of options to send with the request.
     * @return \Fungku\HubSpot\Http\Response
     */
    protected function requestUrl($method, $url, $options = [])
    {
        $options['headers']['User-Agent'] = self::USER_AGENT;
        $options['headers']['Authorization'] = 'Bearer ' . $this->privateAppToken;

        return $this->client->$method($url, $options);
    }

    /**
     * Build the request to the HubSpot API.
     *
     * @param  string $method      The HTTP request verb.
     * @param  string $endpoint    The HubSpot API endpoint.
     * @param  array  $options     An array of options to send with the request.
     * @param  string $queryString A query string to send with the request.
     * @return \Fungku\HubSpot\Http\Response
     */
    protected function request($method, $endpoint, $options = [], $queryString = null)
    {
        $url = $this->generateUrl($endpoint, $queryString);
        return $this->requestUrl($method, $url, $options);
    }

    /**
     * Generate the full endpoint url, including query string.
     *
     * @param  string $endpoint    The HubSpot API endpoint.
     * @param  string $queryString The query string to send to the endpoint.
     * @return string
     */
    protected function generateUrl($endpoint, $queryString = null)
    {
        return $this->baseUrl . $endpoint . '?' . $queryString;
    }

    /**
     * Build a query string from an array.
     *
     * @param  array $query
     * @return string
     */
    protected function buildQueryString($query = [])
    {
        return QueryBuilder::build($query);
    }

    /**
     * @param  string $value
     * @return string
     */
    protected function urlEncode($value)
    {
        return QueryBuilder::encode($value, $this->urlEncoding);
    }
}
