<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;

/**
 * Class YesNoFormatter formats non multiple checkboxes with yes and no.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class YesNoFormatter implements ValueFormatter
{
    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * YesNoFormatter constructor.
     *
     * @param Translator $translator Translator.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        if (empty($fieldDefinition['inputType']) || $fieldDefinition['inputType'] !== 'checkbox') {
            return false;
        }

        if (empty($fieldDefinition['eval']['multiple'])) {
            return true;
        }

        return !$fieldDefinition['eval']['multiple'];
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        return $this->translator->translate(($value == '' ? 'no' : 'yes'), 'MSC');
    }
}
