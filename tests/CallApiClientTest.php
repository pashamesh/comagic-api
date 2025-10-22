<?php

declare(strict_types=1);

use Uiscom\CallApiClient;
use Uiscom\CallApiConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** @covers CallApiClient */
final class CallApiClientTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $config = new CallApiConfig(null, null, 'access_token');
        $this->assertInstanceOf(
            CallApiClient::class,
            new CallApiClient($config)
        );

        $this->assertInstanceOf(
            CallApiClient::class,
            new CallApiClient($config, new Client())
        );
    }

    public function testCanObtainAccessTokenByLoginAndPassword(): void
    {
        $config = new CallApiConfig('login', 'password');
        $httpClient = $this->createMock(Client::class);

        $expectedNewAccessToken = 'new-access-token';
        $expectedBaseUri = rtrim($config->getEntryPoint(), '/') . '/v4.0';
        $expectedHttpLoginPayload = [
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => 'login.user',
            'params' => [
                'login' => $config->getLogin(),
                'password' => $config->getPassword(),
            ],
        ];
        $expectedHttpListCallsPayload = [
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => 'list.calls',
            'params' => [
                'access_token' => $expectedNewAccessToken,
            ],
        ];
        $httpClient->expects($this->exactly(2))
            ->method('post')
            ->with(
                $expectedBaseUri,
                $this->logicalOr(
                    $this->equalTo(['json' => $expectedHttpLoginPayload]),
                    $this->equalTo(['json' => $expectedHttpListCallsPayload])
                )
            )
            ->willReturnOnConsecutiveCalls(
                new Response(
                    200,
                    [],
                    json_encode([
                        'result' => [
                            'data' => [
                                'access_token' => $expectedNewAccessToken,
                                'expire_at' => time() + 3600,
                            ],
                        ],
                    ])
                ),
                new Response(
                    200,
                    [],
                    json_encode(['result' => ['data' => []]])
                )
            );

        $client = new CallApiClient($config, $httpClient);

        $this->assertNull($client->getAccessToken());

        $client->listCalls();

        $this->assertEquals(
            $expectedNewAccessToken,
            $client->getAccessToken()
        );
    }


    /** @dataProvider apiCallsProvider */
    public function testCanDoApiCalls(
        string $method,
        string $expectedApiMethod,
        array $payload,
        string $response
    ): void {
        $config = new CallApiConfig(null, null, 'access_token');
        $httpClient = $this->createMock(Client::class);

        $expectedBaseUri = rtrim($config->getEntryPoint(), '/') . '/v4.0';
        $expectedHttpPayload = [
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => $expectedApiMethod,
            'params' => array_merge($payload, [
                'access_token' => $config->getAccessToken(),
            ]),
        ];

        $httpClient->expects($this->once())
            ->method('post')
            ->with($expectedBaseUri, ['json' => $expectedHttpPayload])
            ->willReturn(new Response(
                200,
                [],
                $response
            ));

        $client = new CallApiClient($config, $httpClient);

        $expectedResponse = json_decode($response)->result;

        $this->assertEquals(
            $expectedResponse->data,
            $client->{$method}($payload)
        );
        $this->assertEquals(
            $expectedResponse->metadata,
            $client->metadata()
        );
    }

    public function apiCallsProvider(): array
    {
        $metadata = [
            'limits' => [
                'day_limit' => 750,
                'day_remaining' => 741,
                'day_reset' => 31492,
                'minute_limit' => 150,
                'minute_remaining' => 149,
                'minute_reset' => 52,
            ],
        ];

        return [
            'List calls' => [
                'listCalls',
                'list.calls',
                [
                    'direction' => 'in',
                    'virtual_phone_number' => '1234567890',
                ],
                json_encode([
                    'result' => [
                        'data' => [
                            'call_session_id' => 206597836,
                            'direction' => 'in',
                            'start_time' => '2016-10-19T12:26:48.418',
                            'virtual_phone_number' => '74951045771',
                            'contact_phone_number' => '74959268686',
                            'external_id' => null,
                            'tags' => [
                                [
                                    'tag_id' => 456,
                                    'tag_name' => 'Целевой',
                                ],
                            ],
                            'legs' => [
                                [
                                    'leg_id' => 287866245,
                                    'calling_phone_number' => '74951045771',
                                    'called_phone_number' => '74959268686...9.2.3.3',
                                    'is_operator' => false,
                                    'employee_id' => null,
                                    'employee_full_name' => null,
                                    'record_call_enabled' => true,
                                    'state' => 'Разговор',
                                ],
                                [
                                    'leg_id' => 287866221,
                                    'calling_phone_number' => '74959268686',
                                    'called_phone_number' => '79262444393',
                                    'is_operator' => true,
                                    'employee_id' => 2345,
                                    'employee_full_name' => 'Тест',
                                    'record_call_enabled' => true,
                                    'state' => 'Разговор',
                                ],
                            ],
                        ],
                        'metadata' => $metadata,
                    ],
                ]),
            ],
            'Start employee call' => [
                'startEmployeeCall',
                'start.employee_call',
                [
                    "first_call" => "employee",
                    "switch_at_once" => true,
                    "media_file_id" => 2701,
                    "show_virtual_phone_number" => false,
                    "virtual_phone_number" => "74993720692",
                    "external_id" => "334otr01",
                    "dtmf_string" => ".1.2.3",
                    "direction" => "in",
                    "contact" => "79260000000",
                    "employee" => [
                        "id" => 25,
                        "phone_number" => "79260000001",
                    ],
                    "contact_message" => [
                        "type" => "tts",
                        "value" => "Привет",
                    ],
                    "employee_message" => [
                        "type" => "media",
                        "value" => "2561",
                    ],
                ],
                json_encode([
                    'result' => [
                        'data' => [
                            'call_session_id' => 237859081,
                        ],
                        'metadata' => $metadata,
                    ],
                ]),
            ],
        ];
    }
}
