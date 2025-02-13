<?php

namespace craftpulse\teamleader\auth\providers;

use craftpulse\teamleader\auth\providers\TeamleaderFocusResourceOwner;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Teamleader
 *
 * @package Nascom\OAuth2\Client\Provider
 */
class TeamleaderFocus extends AbstractProvider
{
    const OAUTH_BASE_URL = 'https://focus.teamleader.eu/oauth2/';
    const API_BASE_URL = 'https://api.focus.teamleader.eu/';

    /**
     * @return string
     */
    public function getGrant(): string
    {
        return 'authorization_code';
    }

    /**
     * @inheritdoc
     */
    public function getBaseAuthorizationUrl(): string
    {
        return self::OAUTH_BASE_URL . 'authorize';
    }

    /**
     * @inheritdoc
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::OAUTH_BASE_URL . 'access_token';
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return self::API_BASE_URL . 'users.me';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getAuthorizationHeaders($token = null): array
    {
        if (!$token instanceof AccessToken) {
            return [];
        }

        return ['Authorization' => 'Bearer ' . $token->getToken()];
    }

    /**
     * @inheritdoc
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new IdentityProviderException(
                '',
                $response->getStatusCode(),
                $response->getBody()->getContents()
            );
        }
    }

    /**
     * @inheritdoc
     *
     * @return TeamleaderFocusResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token): TeamleaderFocusResourceOwner
    {
        if (!isset($response['data'])) {
            throw new \RuntimeException('Unexpected response from the resource owner call');
        }

        return new TeamleaderFocusResourceOwner($response['data']);
    }
}
