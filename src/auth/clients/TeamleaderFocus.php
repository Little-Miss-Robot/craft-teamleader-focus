<?php

namespace craftpulse\teamleader\auth\clients;

use Craft;

use craftpulse\teamleader\auth\clients\TeamleaderFocusResourceOwner;
use craftpulse\teamleader\auth\grant\TeamleaderFocusRefreshTokenGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TeamleaderFocus
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.0.0
 *
 */
class TeamleaderFocus extends AbstractProvider
{
    use BearerAuthorizationTrait;

    const OAUTH_BASE_URL = 'https://focus.teamleader.eu/oauth2/';
    const API_BASE_URL = 'https://api.focus.teamleader.eu/';

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $this->getGrantFactory()->setGrant('refresh_token', new TeamleaderFocusRefreshTokenGrant());
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
        if (!in_array($response->getStatusCode(), [200, 201, 204], true)) {
            throw new IdentityProviderException(
                'Teamleader API error: ' . $response->getStatusCode(),
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
