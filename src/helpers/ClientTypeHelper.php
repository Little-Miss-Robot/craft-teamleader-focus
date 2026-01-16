<?php
/**
 * Teamleader plugin for Craft CMS
 *
 * @link      https://craft-pulse.com
 * @copyright Copyright (c) 2025 CraftPulse
 */

namespace craftpulse\teamleader\helpers;

use craftpulse\teamleader\fields\formie\ClientType;

use verbb\formie\elements\Submission;

/**
 * Class ClientTypeHelper
 *
 * Helper class for determining the client type (B2B/B2C) from a form submission.
 *
 * @author      CraftPulse
 * @package     Teamleader
 * @since       5.2.0
 */
class ClientTypeHelper
{
    /**
     * Get the client type from a submission.
     *
     * Searches the submission's form for a ClientType field and returns its value.
     * Defaults to 'company' (B2B) if no ClientType field is found.
     *
     * @param Submission $submission The form submission
     * @return string The client type ('company' or 'contact')
     */
    public static function getClientType(Submission $submission): string
    {
        $form = $submission->getForm();

        if (!$form) {
            return ClientType::TYPE_COMPANY;
        }

        foreach ($form->getCustomFields() as $field) {
            if ($field instanceof ClientType) {
                $value = $submission->getFieldValue($field->handle);

                if ($value === ClientType::TYPE_CONTACT) {
                    return ClientType::TYPE_CONTACT;
                }

                return ClientType::TYPE_COMPANY;
            }
        }

        // Default to company (B2B) if no ClientType field found
        return ClientType::TYPE_COMPANY;
    }

    /**
     * Check if the submission is a B2B (company) request.
     *
     * @param Submission $submission The form submission
     * @return bool True if B2B, false if B2C
     */
    public static function isCompanyRequest(Submission $submission): bool
    {
        return self::getClientType($submission) === ClientType::TYPE_COMPANY;
    }

    /**
     * Check if the submission is a B2C (contact-only) request.
     *
     * @param Submission $submission The form submission
     * @return bool True if B2C, false if B2B
     */
    public static function isContactRequest(Submission $submission): bool
    {
        return self::getClientType($submission) === ClientType::TYPE_CONTACT;
    }
}
