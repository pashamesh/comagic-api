<?php

declare(strict_types=1);

namespace Uiscom;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use stdClass;

class CallApiClient
{
    private string $version = 'v4.0';
    private ?string $accessToken = null;
    private ?int $accessTokenExpires = null;
    private ?string $login = null;
    private ?string $password = null;
    private ?Client $client = null;

    /**
     * Last response metadata
     *
     */
    private ?stdClass $metadata = null;
    private string $baseUri;

    public function __construct(CallApiConfig $config, ?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json; charset=UTF-8',
            ],
        ]);

        $this->accessToken = $config->getAccessToken();
        $this->login = $config->getLogin();
        $this->password = $config->getPassword();
        $this->baseUri = rtrim($config->getEntryPoint(), '/') .
            '/' . $this->version;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    private function refreshAccessToken(): void
    {
        // Check if access token is not expired
        if (
            $this->accessToken && (is_null($this->accessTokenExpires)
                || $this->accessTokenExpires > (time() + 60))
        ) {
            return;
        }

        $response = $this->doRequest(
            'login.user',
            [
                'login' => $this->login,
                'password' => $this->password,
            ]
        );

        $this->accessToken = $response->access_token;
        $this->accessTokenExpires = $response->expire_at;
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
        $this->refreshAccessToken();

        $camelCaseMethod = preg_replace(
            '~(.)(?=[A-Z])~',
            '$1_',
            $camelCaseMethod
        );

        $method = strtolower(preg_replace('~_~', '.', $camelCaseMethod, 1));

        $params = ['access_token' => $this->accessToken];
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
            $response = $this->client->post($this->baseUri, ['json' => $payload]);

            $responseBody = json_decode($response->getBody()->getContents());

            if (isset($responseBody->result)) {
                $this->metadata = $responseBody->result->metadata ?? null;
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
