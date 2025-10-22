<?php

declare(strict_types=1);

use Uiscom\CallApiConfig;
use PHPUnit\Framework\TestCase;

/** @covers CallApiConfig */
final class CallApiConfigTest extends TestCase
{
    /** @dataProvider validConfigProvider */
    public function testCanBeCreatedFromValidConfig(
        ?string $login = null,
        ?string $password = null,
        ?string $accessToken = null,
        ?string $entryPoint = null
    ): void {
        $config = new CallApiConfig($login, $password, $accessToken, $entryPoint);

        $this->assertInstanceOf(CallApiConfig::class, $config);
        $this->assertEquals($login, $config->getLogin());
        $this->assertEquals($password, $config->getPassword());
        $this->assertEquals($accessToken, $config->getAccessToken());
        $this->assertEquals(
            $entryPoint ?: 'https://callapi.comagic.ru/',
            $config->getEntryPoint()
        );
    }

    /** @dataProvider invalidConfigProvider */
    public function testThrowsExceptionFromInvalidConfig(
        ?string $login = null,
        ?string $password = null,
        ?string $accessToken = null
    ): void {
        $this->expectException(\InvalidArgumentException::class);

        new CallApiConfig($login, $password, $accessToken);
    }

    public function validConfigProvider(): array
    {
        return [
            'accessToken' => [
                null,
                null,
                'some-access-token',
            ],
            'loginAndPassword' => [
                'some-login',
                's0me-passw0rd',
            ],
            'both' => [
                'some-access-token',
                'some-login',
                's0me-passw0rd',
            ],
        ];
    }

    public function invalidConfigProvider(): array
    {
        return [
            'empty' => [
            ],
            'emptyAccessToken' => [
                null,
                null,
                '',
            ],
            'onlyLogin' => [
                'some-login',
            ],
            'onlyPassword' => [
                null,
                's0me-passw0rd',
            ],
            'emptyLogin' => [
                '',
                's0me-passw0rd',
            ],
            'emptyPassword' => [
                'some-login',
                '',
            ],
        ];
    }
}
