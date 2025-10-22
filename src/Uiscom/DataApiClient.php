<?php

namespace Uiscom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use stdClass;

class DataApiClient
{
    private string $version = 'v2.0';
    private DataApiConfig $config;
    private Client $client;

    public function __construct(DataApiConfig $config, ?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json; charset=UTF-8',
            ],
        ]);
        $this->config = $config;
    }

    private function getBaseUri(): string
    {
        return rtrim($this->config->getEntryPoint(), '/') .
            '/' . $this->version;
    }

    /**
     * Get last response metadata
     *
     */
    public function metadata(): ?stdClass
    {
        return $this->metadata;
    }

    /**
     * Magic method for API calls
     *
     */
    public function __call(string $camelCaseMethod, array $arguments)
    {
        $camelCaseMethod = preg_replace(
            '~(.)(?=[A-Z])~',
            '$1_',
            $camelCaseMethod
        );

        $method = strtolower(preg_replace('~_~', '.', $camelCaseMethod, 1));

        $params = ['access_token' => $this->config->getAccessToken()];
        if (isset($arguments[0])) {
            $params = array_merge($params, $arguments[0]);
        }

        return $this->doRequest($method, $params);
    }

    /**
     * @throws \Exception
     */
    private function doRequest(string $method, array $params)
    {
        $payload = [
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => $method,
            'params' => $params,
        ];

        try {
            $response = $this->client->post($this->getBaseUri(), ['json' => $payload]);

            $responseBody = json_decode($response->getBody()->getContents());

            if (isset($responseBody->result)) {
                $this->metadata = $responseBody->result->metadata;
            }

            if (isset($responseBody->error)) {
                throw new \Exception(
                    "{$responseBody->error->code} {$responseBody->error->message}"
                );
            }

            return $responseBody->result->data;
        } catch (TransferException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
