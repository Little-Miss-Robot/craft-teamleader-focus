<?php

namespace craftpulse\teamleader\integrations\formie\fields;

use Craft;
use craft\base\ElementInterface;

use craftpulse\teamleader\integrations\formie\enums\RequestTypes;

use verbb\formie\base\OptionsField;
use verbb\formie\helpers\SchemaHelper;
use verbb\formie\models\HtmlTag;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use yii\base\Exception;

class TeamleaderFocusRequestType extends OptionsField
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('formie', 'Request Type');
    }

    public static function getSvgIconPath(): string
    {
        return 'teamleader-focus/integrations/formie/_formfields/icon-mask.svg';
    }

    // Public Methods
    // =========================================================================
    public function getDefaultOptions(): array
    {
        return [
            [
                'label' => Craft::t('formie', 'Select an option'),
                'value' => '',
                'isOptgroup' => false,
                'isDefault' => true,
            ],
        ];
    }

    /**
     * @TODO => Create ENUMS here as options
     * @return array
     */
    public function getFieldOptions(): array
    {
        return [
            [
                'label' => RequestTypes::Contact->typeAsLabel(),
                'value' => RequestTypes::Contact->value,
                'isOptgroup' => false,
            ],
            [
                'label' => RequestTypes::Company->typeAsLabel(),
                'value' => RequestTypes::Company->value,
                'isOptgroup' => false,
            ],
        ];
    }

    /**
     * @throws SyntaxError
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getPreviewInputHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('formie/_formfields/dropdown/preview', [
            'field' => $this,
        ]);
    }

    public function defineGeneralSchema(): array
    {
        return [
            SchemaHelper::labelField(),
        ];
    }

    public function defineSettingsSchema(): array
    {
        return [
            SchemaHelper::lightswitchField([
                'label' => Craft::t('formie', 'Required Field'),
                'help' => Craft::t('formie', 'Whether this field should be required when filling out the form.'),
                'name' => 'required',
            ]),
            SchemaHelper::textField([
                'label' => Craft::t('formie', 'Error Message'),
                'help' => Craft::t('formie', 'When validating the form, show this message if an error occurs. Leave empty to retain the default message.'),
                'name' => 'errorMessage',
                'if' => '$get(required).value',
            ]),
            SchemaHelper::prePopulate(),
            SchemaHelper::includeInEmailField(),
            SchemaHelper::emailNotificationValue([
                'options' => [
                    ['label' => Craft::t('formie', 'Label'), 'value' => 'label'],
                    ['label' => Craft::t('formie', 'Value'), 'value' => 'value'],
                ],
            ]),
        ];
    }

    public function defineAppearanceSchema(): array
    {
        return [
            SchemaHelper::visibility(),
            SchemaHelper::labelPosition($this),
            SchemaHelper::instructions(),
            SchemaHelper::instructionsPosition($this),
        ];
    }

    public function defineAdvancedSchema(): array
    {
        return [
            SchemaHelper::handleField(),
            SchemaHelper::cssClasses(),
            SchemaHelper::containerAttributesField(),
            SchemaHelper::inputAttributesField(),
        ];
    }

    public function defineConditionsSchema(): array
    {
        return [
            SchemaHelper::enableConditionsField(),
            SchemaHelper::conditionsField(),
        ];
    }

    public function defineHtmlTag(string $key, array $context = []): ?HtmlTag
    {
        $form = $context['form'] ?? null;
        $errors = $context['errors'] ?? null;

        if ($key === 'fieldInput') {
            $optionValue = $this->getFieldInputOptionValue($context);

            return new HtmlTag('select', [
                'id' => $this->getHtmlId($form, $optionValue),
                'class' => [
                    'fui-select',
                    $errors ? 'fui-error' : false,
                ],
                'name' => $this->getHtmlName(),
                'multiple' => null,
                'required' => $this->required ? true : null,
                'data' => [
                    'fui-id' => $this->getHtmlDataId($form, $optionValue),
                    'required-message' => Craft::t('formie', $this->errorMessage) ?: null,
                ],
            ], $this->getInputAttributes());
        }

        return parent::defineHtmlTag($key, $context);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws Exception
     * @throws LoaderError
     */
    protected function cpInputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
    {
        return Craft::$app->getView()->renderTemplate('formie/_formfields/dropdown/input', [
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
            'options' => $this->translatedOptions(),
        ]);
    }

    protected function optionsSettingLabel(): string
    {
        return Craft::t('app', 'Dropdown Options');
    }
}
