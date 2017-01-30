<?php

namespace CoMagic;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CallApiClient
{
    /**
     * Call API entry point
     *
     * @var string
     */
    private $_entryPoint = 'https://callapi.comagic.ru/';

    /**
     * Call API version to use
     *
     * @var string
     */
    private $_version = 'v4.0';

    /**
     * Call API access token
     *
     * @var string
     */
    private $_accessToken = null;

    /**
     * Call API access token expiration time
     *
     * @var string
     */
    private $_accessTokenExpires = null;

    /**
     * Call API login
     *
     * @var string
     */
    private $_login = null;

    /**
     * Call API password
     *
     * @var string
     */
    private $_password = null;

    /**
     * Call API Guzzle client
     *
     * @var GuzzleHttp\Client
     */
    private $_client = null;

    /**
     * Call API last response metadata
     *
     */
    private $_metadata = null;

    /**
     * Init CoMagic Call API client
     *
     * @param array $config
     */
    public function __construct($config)
    {
        if (!(isset($confog['access_token']) ||
            isset($config['login']) && isset($config['password'])))
        {
            throw new \Exception('Access token and/or login+password required');
        }

        if (!empty($config['endpoint'])) {
            $this->_entryPoint = $config['endpoint'];
        }

        $this->_client = new Client([
            'base_uri' => rtrim($this->_entryPoint, '/') .
                '/' . $this->_version,
            'headers' => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json; charset=UTF-8'
            ]
        ]);

        if (!empty($config['access_token'])) {
            $this->_accessToken = $config['access_token'];
        }

        if (!empty($config['login']) && !empty($config['password']))
        {
            $this->_login    = $config['login'];
            $this->_password = $config['password'];
        }
    }

    private function _checkLogin()
    {
        // Check if access token is not expired
        if ($this->_accessToken && (is_null($this->_accessTokenExpires) ||
            $this->_accessTokenExpires > (time() + 60)))
        {
            return true;
        }

        $data = $this->_doRequest(
            'login.user',
            [
                'login'    => $this->_login,
                'password' => $this->_password
            ]
        );

        $this->_accessToken        = $data->access_token;
        $this->_accessTokenExpires = $data->expire_at;
    }

    /**
     * Get last response metadata
     *
     * @return string
     */
    public function metadata()
    {
        return $this->_metadata;
    }

    /**
     * Magic method for API calls
     *
     * @param string $camelCaseMethod
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($camelCaseMethod, $arguments)
    {
        $this->_checkLogin();

        $method = strtolower(
            preg_replace('/(.)(?=[A-Z])/', '$1.', $camelCaseMethod)
        );

        $params = ['access_token' => $this->_accessToken];
        if (isset($arguments[0]))
        {
            $params = array_merge($params, $arguments[0]);
        }

        return $this->_doRequest($method, $params);
    }

    private function _doRequest($method, $params)
    {
        $payload = [
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => $method,
            'params' => $params
        ];

        try
        {
            $response = $this->_client->post('', ['json' => $payload]);

            $responseBody = json_decode($response->getBody());

            if (isset($responseBody->result))
            {
                $this->_metadata = $responseBody->result->metadata;
            }

            if (isset($responseBody->error))
            {
                throw new \Exception(
                    $responseBody->error->code . ' ' .
                        $responseBody->error->message
                );
            }

            return $responseBody->result->data;
        }
        catch (TransferException $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
}