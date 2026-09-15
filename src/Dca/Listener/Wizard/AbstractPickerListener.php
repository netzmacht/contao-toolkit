<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

/**
 * AbstractPicker is the base class for a picker wizard.
 */
abstract class AbstractPickerListener extends AbstractWizardListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_picker.html.twig';
}
