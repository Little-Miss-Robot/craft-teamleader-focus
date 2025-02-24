<?php

declare(strict_types=1);

namespace craftpulse\teamleader\auth\grant;

use League\OAuth2\Client\Grant\AbstractGrant;

/**
 * Class TeamleaderFocusRefreshTokenGrant
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.0.0
 *
 */
class TeamleaderFocusRefreshTokenGrant extends AbstractGrant
{
    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'refresh_token';
    }

    /**
     * @return string[]
     */
    protected function getRequiredRequestParameters(): array
    {
        return [
            'refresh_token',
        ];
    }

    /**
     * @param array $defaults
     * @param array $options
     * @return array
     */
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
