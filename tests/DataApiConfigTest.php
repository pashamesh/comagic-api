<?php

declare(strict_types=1);

use Uiscom\DataApiConfig;
use PHPUnit\Framework\TestCase;

/** @covers DataApiConfig */
final class DataApiConfigTest extends TestCase
{
    public function testCanBeCreatedFromValidConfig(): void
    {
        $config = new DataApiConfig('some-access-token');

        $this->assertInstanceOf(DataApiConfig::class, $config);
        $this->assertEquals('some-access-token', $config->getAccessToken());
        $this->assertEquals(
            'https://dataapi.comagic.ru/',
            $config->getEntryPoint()
        );
    }

    public function testThrowsExceptionFromInvalidConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new DataApiConfig('');
    }
}
