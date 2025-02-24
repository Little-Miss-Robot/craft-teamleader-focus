<?php

namespace craftpulse\teamleader\auth\providers;

use craftpulse\teamleader\auth\clients\TeamleaderFocus as TeamleaderFocusClient;
use verbb\auth\base\ProviderTrait;
use verbb\auth\models\Token;

class TeamleaderFocus extends TeamleaderFocusClient
{
    // Traits
    // =========================================================================

    use ProviderTrait;


    // Public Methods
    // =========================================================================

    public function getBaseApiUrl(?Token $token): ?string
    {
        return 'https://api.focus.teamleader.eu/';
    }
}
