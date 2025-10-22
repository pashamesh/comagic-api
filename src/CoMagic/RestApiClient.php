<?php

namespace CoMagic;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class RestApiClient
{
    /**
     * Rest API entry point
     *
     * @var string
     */
    private $_entryPoint = 'https://api.comagic.ru/api/';

    /**
     * Rest API version to use
     *
     * @var string
     */
    private $_version = 'v1';

    /**
     * Rest API session key
     *
     * @var string
     */
    private $_sessionKey = null;

    /**
     * Rest API Guzzle client
     *
     * @var Client
     */
    private $_client = null;

    /**
     * Init CoMagic Rest API client
     *
     * @param array $config
     */
    public function __construct($config)
    {
        if (!empty($config['endpoint']['rest_api'])) {
            $this->_entryPoint = $config['endpoint']['rest_api'];
        }

        $this->_client = new Client([
            'base_uri' => $this->_entryPoint,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if (!empty($config['login']) && !empty($config['password'])) {
            $this->login($config['login'], $config['password']);
        }
    }

    /**
     * Get current session key
     *
     * @return string
     */
    public function getSessionKey()
    {
        return $this->_sessionKey;
    }

    /**
     * Do login and set session key
     *
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function login($login, $password)
    {
        $payload =  [
            'form_params' => [
                'login' => $login,
                'password' => $password,
            ],
        ];

        $data = $this->_doRequest('login', $payload);

        if (isset($data->session_key)) {
            $this->_sessionKey = $data->session_key;

            return true;
        }

        return false;
    }

    /**
     * Magic method for API calls
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $payload = ['session_key' => $this->_sessionKey];

        if (is_null($this->_sessionKey)) {
            throw new Exception('You are not logged in');
        }

        if (!empty($arguments[0])) {
            $payload = array_merge($payload, $arguments[0]);
        }

        if ($this->_mapMethod($method) === 'GET') {
            $payload = ['query' => $payload];
        }

        return $this->_doRequest($this->_version . '/' . $method, $payload);
    }

    /**
     * Do request with response and exceptions handling
     *
     * @param string $method
     * @param array $payload
     * @return mixed
     * @throws Exception
     */
    private function _doRequest($method, $payload)
    {
        try {
            $response = $this->_client->request(
                $this->_mapMethod($method),
                $method . '/',
                $payload
            );

            $responseBody = json_decode($response->getBody());

            if (!$responseBody->success) {
                throw new Exception($responseBody->message);
            }

            return $responseBody->data;
        } catch (TransferException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Detect request method by API method name
     *
     * @param string $name
     * @return string
     */
    private function _mapMethod($name)
    {
        switch ($name) {
            case 'create_agent_customer':
            case 'create_site':
            case 'create_ac_condition_group':
            case 'create_ac_condition':
            case 'delete_ac_condition':
            case 'buy_number':
            case 'create_dialing_operation':
            case 'create_numa_list':
            case 'add_number_to_numa_list':
            case 'remove_number_from_numa_list':
            case 'tag_communication':
            case 'tag_communication_sale':
            case 'untag_communication':
                return 'POST';

            default:
                return 'GET';
        }
    }
}
