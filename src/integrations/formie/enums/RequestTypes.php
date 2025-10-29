<?php

namespace craftpulse\teamleader\integrations\formie\enums;

use Craft;
use craft\enums\Color;
use craft\helpers\Cp;

/**
 * Bulk Operation Types enum
 */
enum RequestTypes: string
{
    case Contact = 'contact';
    case Company = 'company';

    /**
     * @return string
     */
    public function typeAsLabel(): string
    {
        return match ($this) {
            self::Contact => Craft::t('formie', 'Contact'),
            self::Company => Craft::t('formie', 'Company'),
        };
    }

    /**
     * @return string
     */
    public function statusLabelHtml(): string
    {
        return Cp::statusLabelHtml([
            'color' => match ($this) {
                self::Contact => Color::Sky,
                self::Company => Color::Blue,
            },
            'label' => $this->typeAsLabel(),
        ]);
    }
}
