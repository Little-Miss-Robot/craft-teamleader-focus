<?php

declare(strict_types=1);

namespace craftpulse\teamleader\auth\grant;

use League\OAuth2\Client\Grant\AbstractGrant;

class TeamleaderFocusRefreshTokenGrant extends AbstractGrant
{
    protected function getName(): string
    {
        return 'refresh_token';
    }

    protected function getRequiredRequestParameters(): array
    {
        return [
            'refresh_token',
        ];
    }

    public function prepareRequestParameters(array $defaults, array $options): array
    {
        return [
            'grant_type' => $this->getName(),
            'client_id' => $defaults['client_id'],
            'client_secret' => $defaults['client_secret'],
            'refresh_token' => $options['refresh_token'],
        ];
    }
}
