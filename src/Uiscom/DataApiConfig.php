<?php

declare(strict_types=1);

namespace Uiscom;

use InvalidArgumentException;

final class DataApiConfig
{
    private ?string $accessToken;

    private string $entryPoint;

    public function __construct(
        ?string $accessToken = null,
        ?string $entryPoint = null
    ) {
        if (empty($accessToken)) {
            throw new InvalidArgumentException(
                'Access token is required'
            );
        }

        $this->accessToken = $accessToken;
        $this->entryPoint = $entryPoint ?: 'https://dataapi.comagic.ru/';
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }
}
