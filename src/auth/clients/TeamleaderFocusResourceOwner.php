<?php

namespace craftpulse\teamleader\auth\clients;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

/**
 * Class TeamleaderFocusResourceOwner
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.0.0
 *
 */
class TeamleaderFocusResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait {
        getValueByKey as arrayAccessorTraitGetValueByKey;
    }

    /**
     * @var array
     */
    protected array $response;

    /**
     * TeamleaderFocusResourceOwner constructor.
     *
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * @inheritdoc
     */
    public function getId(): array|int|string|null
    {
        return $this->getValueByKey('id');
    }

    /**
     * @return array|string|null
     */
    public function getAccount(): array|string|null
    {
        return $this->getValueByKey('account', []);
    }

    /**
     * @return array|string|null
     */
    public function getFirstName(): array|string|null
    {
        return $this->getValueByKey('first_name', '');
    }

    /**
     * @return array|string|null
     */
    public function getLastName(): array|string|null
    {
        return $this->getValueByKey('last_name', '');
    }

    /**
     * @return array|string|null
     */
    public function getEmail(): array|string|null
    {
        return $this->getValueByKey('email', '');
    }

    /**
     * @return array|string|null
     */
    public function getLanguage(): array|string|null
    {
        return $this->getValueByKey('language', '');
    }

    /**
     * @return array|string|null
     */
    public function getTelephones(): array|string|null
    {
        return $this->getValueByKey('telephones', []);
    }

    /**
     * @return array|string|null
     */
    public function getFunction(): array|string|null
    {
        return $this->getValueByKey('function', '');
    }

    /**
     * @return array|string|null
     */
    public function getTimezone(): array|string|null
    {
        return $this->getValueByKey('time_zone', '');
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->response;
    }

    /**
     * Just the ArrayAccessorTrait's getValueByKey method, but with the first
     * parameter prefilled.
     *
     * @param $key
     * @param mixed $default
     * @return mixed
     */
    private function getValueByKey(string $key, mixed $default = null): mixed
    {
        return $this->arrayAccessorTraitGetValueByKey(
            $this->response,
            $key,
            $default
        );
    }
}
