<?php
/**
 * Teamleader plugin for Craft CMS
 *
 * This plugin integrates with teamleader focus to generate and manage deals, and comes with a Formie integration.
 *
 * @link      https://craft-pulse.com
 * @copyright Copyright (c) 2025 CraftPulse
 */

namespace craftpulse\teamleader\integrations\formie;

use Craft;
use craft\helpers\App;

use craftpulse\teamleader\auth\providers\TeamleaderFocus as TeamleaderFocusProvider;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use verbb\auth\base\OAuthProviderInterface;
use verbb\auth\models\Token;
use verbb\formie\base\Crm;
use yii\base\Exception;

/**
 * Class TeamleaderFocus
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.0.0
 *
 */
class TeamleaderFocus extends Crm implements OAuthProviderInterface
{

    // Static Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('formie', 'Teamleader Focus');
    }

    public static function getOAuthProviderClass(): string
    {
        return TeamleaderFocusProvider::class;
    }

    public static function supportsOAuthConnection(): bool
    {
        return true;
    }

    // Public Methods
    // =========================================================================
    /**
     * @return string
     */
    public function getIconUrl(): string
    {
        return Craft::$app->getAssetManager()->getPublishedUrl("@craftpulse/teamleader/icon-mask.svg", true);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Craft::t('formie', 'This is a Teamleader Focus lead creation integration.');
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function getSettingsHtml(): string
    {
        $settings = $this->getSettingsHtmlVariables();

        return Craft::$app->getView()->renderTemplate('teamleader/integrations/formie/_plugin-settings', $settings);
    }

    /**
     * @param $form
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getFormSettingsHtml($form): string
    {
        $formSettings = $this->getFormSettingsHtmlVariables($form);

        return Craft::$app->getView()->renderTemplate('teamleader/integrations/formie/_form-settings', $formSettings);
    }

    /**
     * @return string
     */
    public function getApiDomain(): string
    {
        return "https://api.focus.teamleader.eu/";
    }

    /**
     * @param Token|null $token
     * @return string|null
     */
    public function getBaseApiUrl(?Token $token): ?string
    {
        return "https://api.focus.teamleader.eu/";
    }

    /**
     * @return array
     */
    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['domain'] = $this->getApiDomain();
        $config['baseApiUrl'] = $this->getApiDomain();
        $config['clientId'] = $this->getClientId();
        $config['clientSecret'] = $this->getClientSecret();
        $config['redirectUri'] = $this->getRedirectUri();

        return $config;
    }

    /**
     * @return array
     */
    public function getAuthorizationUrlOptions(): array
    {
        $options = parent::getAuthorizationUrlOptions();

        $options['scope'] = [
        ];

        return $options;
    }
}
