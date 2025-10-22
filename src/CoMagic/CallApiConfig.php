<?php

declare(strict_types=1);

namespace CoMagic;

use InvalidArgumentException;

final class CallApiConfig
{
    private ?string $login;
    private ?string $password;
    private ?string $accessToken;

    private string $entryPoint;

    public function __construct(
        ?string $login = null,
        ?string $password = null,
        ?string $accessToken = null,
        ?string $entryPoint = null
    ) {
        if (empty($login) && empty($password) && empty($accessToken)) {
            throw new InvalidArgumentException(
                'Access token and/or login+password are required'
            );
        }

        if (empty($login) xor empty($password)) {
            throw new InvalidArgumentException(
                'Login and password cannot be empty'
            );
        }

        $this->login = $login;
        $this->password = $password;
        $this->accessToken = $accessToken;
        $this->entryPoint = $entryPoint ?: 'https://callapi.comagic.ru/';
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getPassword(): ?string
    {
        return $this->password;
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
