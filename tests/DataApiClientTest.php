<?php

declare(strict_types=1);

use Uiscom\DataApiClient;
use Uiscom\DataApiConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/** @covers CallApiClient */
final class DataApiClientTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $config = new DataApiConfig('access_token');
        $this->assertInstanceOf(
            DataApiClient::class,
            new DataApiClient($config)
        );

        $this->assertInstanceOf(
            DataApiClient::class,
            new DataApiClient($config, new Client())
        );
    }


    /** @dataProvider apiCallsProvider */
    public function testCanDoApiCalls(
        string $method,
        string $expectedApiMethod,
        array $payload,
        string $response
    ): void {
        $config = new DataApiConfig('access_token');
        $httpClient = $this->createMock(Client::class);

        $expectedBaseUri = rtrim($config->getEntryPoint(), '/') . '/v2.0';
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

        $client = new DataApiClient($config, $httpClient);

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
            'Get calls report' => [
                'getCallsReport',
                'get.calls_report',
                [
                    "user_id" => "number",
                    "offset" => "number",
                    "limit" => "number",
                    "date_from" => "string",
                    "date_till" => "string",
                    "filter" => [],
                    "sort" => [
                        [
                            "field" => "string",
                            "order" => "string",
                        ],
                    ],
                    "fields" => [
                        "string",
                    ],
                ],
                json_encode([
                    'result' => [
                        'data' => [
                            'id' => 'number',
                            'start_time' => 'iso8601',
                            'finish_time' => 'iso8601',
                            'virtual_phone_number' => 'string',
                            'is_transfer' => 'boolean',
                            'finish_reason' => 'enum',
                            'direction' => 'enum',
                            'source' => 'enum',
                            'communication_number' => 'number',
                            'communication_page_url' => 'string',
                            'communication_id' => 'number',
                            'communication_type' => 'enum',
                            'is_lost' => 'boolean',
                            'cpn_region_id' => 'number',
                            'cpn_region_name' => 'string',
                            'cpn_country_name' => 'string',
                            'cpn_country_id' => 'string',
                            'wait_duration' => 'number',
                            'total_wait_duration' => 'number',
                            'lost_call_processing_duration' => 'number',
                            'talk_duration' => 'number',
                            'clean_talk_duration' => 'number',
                            'total_duration' => 'number',
                            'postprocess_duration' => 'number',
                            'ua_client_id' => 'string',
                            'ym_client_id' => 'string',
                            'sale_date' => 'iso8601',
                            'sale_cost' => 'number',
                            'search_query' => 'string',
                            'search_engine' => 'string',
                            'referrer_domain' => 'string',
                            'referrer' => 'string',
                            'entrance_page' => 'string',
                            'gclid' => 'string',
                            'yclid' => 'string',
                            'ymclid' => 'string',
                            'ef_id' => 'string',
                            'channel' => 'enum',
                            'site_id' => 'number',
                            'site_domain_name' => 'string',
                            'campaign_id' => 'number',
                            'campaign_name' => 'string',
                            'auto_call_campaign_name' => 'string',
                            'visit_other_campaign' => 'boolean',
                            'visitor_id' => 'number',
                            'person_id' => 'number',
                            'visitor_type' => 'enum',
                            'visitor_session_id' => 'number',
                            'visits_count' => 'number',
                            'visitor_first_campaign_id' => 'number',
                            'visitor_first_campaign_name' => 'string',
                            'visitor_city' => 'string',
                            'visitor_region' => 'string',
                            'visitor_country' => 'string',
                            'visitor_device' => 'enum',
                            'last_answered_employee_id' => 'number',
                            'last_answered_employee_full_name' => 'string',
                            'last_answered_employee_rating' => 'number',
                            'first_answered_employee_id' => 'number',
                            'first_answered_employee_full_name' => 'string',
                            'scenario_id' => 'number',
                            'scenario_name' => 'string',
                            'call_api_external_id' => 'string',
                            'call_api_request_id' => 'number',
                            'contact_phone_number' => 'string',
                            'contact_full_name' => 'string',
                            'contact_id' => 'number',
                            'utm_source' => 'string',
                            'utm_medium' => 'string',
                            'utm_term' => 'string',
                            'utm_content' => 'string',
                            'utm_campaign' => 'string',
                            'openstat_ad' => 'string',
                            'openstat_campaign' => 'string',
                            'openstat_service' => 'string',
                            'openstat_source' => 'string',
                            'eq_utm_source' => 'string',
                            'eq_utm_medium' => 'string',
                            'eq_utm_term' => 'string',
                            'eq_utm_content' => 'string',
                            'eq_utm_campaign' => 'string',
                            'eq_utm_referrer' => 'string',
                            'eq_utm_expid' => 'string',
                            'attributes' => [],
                            'call_records' => [],
                            'voice_mail_records' => [],
                            'recognized_text' => [
                                [
                                    'id' => 'number',
                                    'phrase' => 'string',
                                    'start_time' => 'iso8601',
                                    'is_operator' => 'boolean',
                                    'edited_phrase' => 'string',
                                ],
                            ],
                            'tags' => [
                                [
                                    'tag_id' => 'number',
                                    'tag_name' => 'string',
                                    'tag_type' => 'enum',
                                    'tag_change_time' => 'iso8601',
                                    'tag_user_id' => 'number',
                                    'tag_user_login' => 'string',
                                    'tag_employee_id' => 'number',
                                    'tag_employee_full_name' => 'string',
                                ],
                            ],
                            'visitor_custom_properties' => [
                                ['property_name' => 'string', 'property_value' => 'string'],
                            ],
                            'segments' => [
                                ['segment_id' => 'number', 'segment_name' => 'string'],
                            ],
                            'employees' => [
                                [
                                    'employee_id' => 'number',
                                    'employee_full_name' => 'string',
                                    'is_answered' => 'boolean',
                                ],
                            ],
                            'operator_phone_number' => 'string',
                            'scenario_operations' => [
                                ['name' => 'string', 'id' => 'number'],
                            ],
                            'source_id' => 'number',
                            'source_name' => 'string',
                            'source_new' => 'string',
                            'channel_new' => 'string',
                            'channel_code' => 'string',
                            'ext_id' => 'string',
                            'properties' => 'string',
                        ],
                        'metadata' => $metadata,
                    ],
                ]),
            ],
        ];
    }
}
