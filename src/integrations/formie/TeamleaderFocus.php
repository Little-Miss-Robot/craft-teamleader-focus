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
use craft\helpers\Json;

use craftpulse\teamleader\auth\providers\TeamleaderFocus as TeamleaderFocusProvider;

use Illuminate\Support\Collection;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use verbb\formie\base\Integration;
use verbb\auth\base\OAuthProviderInterface;
use verbb\auth\models\Token;
use verbb\formie\base\Crm;
use verbb\formie\elements\Submission;
use verbb\formie\errors\IntegrationException;
use verbb\formie\models\IntegrationField;
use verbb\formie\models\IntegrationFormSettings;

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

    // Properties
    // =========================================================================

    /**
     * @var bool
     */
    public bool $mapToContacts = false;
    /**
     * @var bool
     */
    public bool $mapToCompanies = false;
    /**
     * @var bool
     */
    public bool $mapToDeals = false;
    /**
     * @var bool
     */
    public bool $linkToCompany = false;
    /**
     * @var string|null
     */
    public ?string $dealTitle = null;

    /**
     * @var array|null
     */
    public ?array $contactsFieldMapping = null;
    /**
     * @var array|null
     */
    public ?array $companiesFieldMapping = null;
    /**
     * @var array|null
     */
    public ?array $dealsFieldMapping = null;

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

    public function sendPayload(Submission $submission): bool
    {
        try {
            $contactValues = $this->getFieldMappingValues($submission, $this->contactsFieldMapping, 'contacts');
            $companyValues = $this->getFieldMappingValues($submission, $this->companiesFieldMapping, 'companies');
            $dealsValues = $this->getFieldMappingValues($submission, $this->dealsFieldMapping, 'deals');

            $userId = null;
            $companyId = null;

            if ($this->mapToContacts) {
                $contactPayload = $this->_prepPayload($contactValues, 'contacts');
                $endpoint = 'contacts.add';

                // First check if we already have a user with the primary email address attached.

                $filterPayload = [
                    'filter' => [
                        'email' => [
                            'type' => 'primary',
                            'email' => $contactValues['email'],
                        ],
                    ]
                ];

                $response = $this->deliverPayload($submission, 'contacts.list', $filterPayload);
                $currentUser = Collection::make($response['data'])->first();

                // Make sure we send a "contacts.update" request if we have an actual response id.
                if(!empty($currentUser['id'])) {
                    $endpoint = 'contacts.update';
                    $contactPayload['id'] = $currentUser['id'];
                }

                $response = $this->deliverPayload($submission, $endpoint, $contactPayload);

                if ($response === false) {
                    return true;
                }

                if($endpoint === 'contacts.add') {
                    $userId = $response['data']['id'] ?? null;

                    if (is_null($userId)) {
                        Integration::error($this, Craft::t('formie', 'Missing return “id” {response}. Sent payload {payload}', [
                            'response' => Json::encode($response),
                            'payload' => Json::encode($contactValues),
                        ]), true);

                        return false;
                    }
                } else {
                    if (!empty($response)) {
                        Integration::error($this, Craft::t('formie', 'Invalid response {response} Sent payload {payload}', [
                            'response' => Json::encode($response),
                            'payload' => Json::encode($contactValues),
                        ]), true);

                        return false;
                    }
                }
            }

            if ($this->mapToCompanies) {
                $companyPayload = $this->_prepPayload($companyValues, 'companies');

                $response = $this->deliverPayload($submission, 'companies.add', $companyPayload);

                if ($response === false) {
                    return true;
                }

                $companyId = $response['data']['id'] ?? null;

                if (is_null($companyId)) {
                    Integration::error($this, Craft::t('formie', 'Missing return “id” {response}. Sent payload {payload}', [
                        'response' => Json::encode($response),
                        'payload' => Json::encode($companyValues),
                    ]), true);

                    return false;
                } else {
                    if($userId && $companyId && $this->linkToCompany) {
                        $linkPayload = [
                            'id' => $userId,
                            'company_id' => $companyId,
                        ];

                        $response = $this->deliverPayload($submission, 'contacts.linkToCompany', $linkPayload);

                        if ($response === false) {
                            return true;
                        }
                    }

                }
            }

            if ($this->mapToDeals && ($userId || $companyId)) {
                $options = [
                    'contact_person_id' => $userId ?? '',
                    'company_id' => $companyId ?? '',
                ];

                $dealPayload = $this->_prepPayload($dealsValues, 'deals', $options);

                $response = $this->deliverPayload($submission, 'deals.create', $dealPayload);

                if ($response === false) {
                    return true;
                }

                $dealId = $response['data']['id'] ?? null;

                if (is_null($dealId)) {
                    Integration::error($this, Craft::t('formie', 'Missing return “id” {response}. Sent payload {payload}', [
                        'response' => Json::encode($response),
                        'payload' => Json::encode($dealsValues),
                    ]), true);

                    return false;
                }
            }
        } catch (Throwable $error) {
            Integration::apiError($this, $error);

            return false;
        }

        return true;
    }

    /**
     * @return IntegrationFormSettings
     * @throws IntegrationException
     */
    public function fetchFormSettings(): IntegrationFormSettings
    {
        $settings = [];

        try {
            if ($this->mapToContacts) {
                $fields = $this->_fetchCustomFields('contact');

                $settings['contacts'] = array_merge([
                    new IntegrationField([
                        'handle' => 'salutation',
                        'name' => Craft::t('formie', 'Salutation'),
                    ]),
                    new IntegrationField([
                        'handle' => 'first_name',
                        'name' => Craft::t('formie', 'First Name'),
                    ]),
                    new IntegrationField([
                        'handle' => 'last_name',
                        'name' => Craft::t('formie', 'Last Name'),
                        'required' => true,
                    ]),
                    new IntegrationField([
                        'handle' => 'email',
                        'name' => Craft::t('formie', 'Email address'),
                        'required' => true,
                    ]),
                    new IntegrationField([
                        'handle' => 'mobile_phone',
                        'name' => Craft::t('formie', 'Mobile number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'phone',
                        'name' => Craft::t('formie', 'Phone number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'language',
                        'name' => Craft::t('formie', 'Language'),
                    ]),
                    new IntegrationField([
                        'handle' => 'marketing_mails_consent',
                        'name' => Craft::t('formie', 'Marketing Mails Consent'),
                        'type' => 'boolean',
                    ]),
                ], $this->_getCustomFields($fields));
            }

            if ($this->mapToCompanies) {
                $fields = $this->_fetchCustomFields('company');

                $settings['companies'] = array_merge([
                    new IntegrationField([
                        'handle' => 'company_name',
                        'name' => Craft::t('formie', 'Company Name'),
                        'required' => true,
                    ]),
                    new IntegrationField([
                        'handle' => 'email',
                        'name' => Craft::t('formie', 'Email address'),
                        'required' => true,
                    ]),
                    new IntegrationField([
                        'handle' => 'mobile_phone',
                        'name' => Craft::t('formie', 'Mobile number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'phone',
                        'name' => Craft::t('formie', 'Phone number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'vat_number',
                        'name' => Craft::t('formie', 'VAT Number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'national_identification_number',
                        'name' => Craft::t('formie', 'National Identification Number'),
                    ]),
                    new IntegrationField([
                        'handle' => 'website',
                        'name' => Craft::t('formie', 'Website'),
                    ]),
                    new IntegrationField([
                        'handle' => 'language',
                        'name' => Craft::t('formie', 'Language'),
                    ]),
                    new IntegrationField([
                        'handle' => 'marketing_mails_consent',
                        'name' => Craft::t('formie', 'Marketing Mails Consent'),
                        'type' => 'boolean',
                    ]),
                ], $this->_getCustomFields($fields));
            }

            if ($this->mapToDeals) {
                $fields = $this->_fetchCustomFields('sale');

                $settings['deals'] = array_merge([], $this->_getCustomFields($fields));
            }
        } catch (Throwable $error) {
            Integration::apiError($this, $error);
        }

        return new IntegrationFormSettings($settings);
    }

    private function _fetchCustomFields(string $context): ?array {
        $filters = [
            'filter' => [
                'context' => $context,
            ]
        ];

        $response = $this->request('POST', 'customFieldDefinitions.list', $filters);
        $customFields = $response['data'];

        if (empty($customFields)) {
            return null;
        } else {
            return Collection::make($customFields)->filter(fn($field) => $field['context'] === $context)->toArray();
        }
    }

    private function _getCustomFields(mixed $fields): array
    {
        $customFields = [];

        foreach ($fields as $field) {
            $type = $field['type'] ?? null;

            if (!$type) {
                continue;
            }

            $customFields[] = new IntegrationField([
                'handle' => $field['id'],
                'name' => $field['label'],
                'type' => $this->_convertFieldType($type),
                'sourceType' => $type,
            ]);
        }

        return $customFields;
    }

    private function _convertFieldType(string $fieldType): string
    {
        $fieldTypes= [
            'multi_select' => IntegrationField::TYPE_ARRAY,
            'date' => IntegrationField::TYPE_DATE,
            'money' => IntegrationField::TYPE_FLOAT,
            'auto_increment' => IntegrationField::TYPE_NUMBER,
            'integer' => IntegrationField::TYPE_NUMBER,
            'number' => IntegrationField::TYPE_NUMBER,
            'boolean' => IntegrationField::TYPE_BOOLEAN,
            'telephone' => IntegrationField::TYPE_PHONE,
        ];

        return $fieldTypes[$fieldType] ?? IntegrationField::TYPE_STRING;
    }

    private function _prepPayload(array $fields, string $context, array $options = []): array
    {
        $payload = $fields;
        $payload['context'] = $context;

        if (in_array($context, ['contacts', 'companies'])) {
            if(isset($payload['email'])) {
                $payload['emails'][] = [
                    'type' => 'primary',
                    'email' => $payload['email'],
                ];
                unset($payload['email']);
            }

            if(isset($payload['phone'])) {
                $payload['telephones'][] = [
                    'type' => 'phone',
                    'number' => $payload['phone'],
                ];
                unset($payload['phone']);
            }

            if(isset($payload['mobile_phone'])) {
                $payload['telephones'][] = [
                    'type' => 'phone',
                    'number' => $payload['mobile_phone'],
                ];
                unset($payload['phone']);
            }

            return $payload;
        }

        if ($context === 'deals') {
            $payload['lead'] = [
                'customer' => [
                    'type' => !is_null($options['company_id']) ? 'company' : 'contact',
                    'id' => !is_null($options['company_id']) ? $options['company_id'] : $options['contact_person_id'],
                ],
                'contact_person_id' => $options['contact_person_id'],
            ];
            $payload['title'] = $this->dealTitle;
        }

        return $payload;
    }
}
