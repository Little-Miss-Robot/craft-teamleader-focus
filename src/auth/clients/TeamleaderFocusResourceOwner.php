<?php

namespace craftpulse\teamleader\auth\clients;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;


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

    public function getAccount(): array|string|null
    {
        return $this->getValueByKey('account', []);
    }

    public function getFirstName(): array|string|null
    {
        return $this->getValueByKey('first_name', '');
    }

    public function getLastName(): array|string|null
    {
        return $this->getValueByKey('last_name', '');
    }

    public function getEmail(): array|string|null
    {
        return $this->getValueByKey('email', '');
    }

    public function getLanguage(): array|string|null
    {
        return $this->getValueByKey('language', '');
    }

    public function getTelephones(): array|string|null
    {
        return $this->getValueByKey('telephones', []);
    }

    public function getFunction(): array|string|null
    {
        return $this->getValueByKey('function', '');
    }

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
