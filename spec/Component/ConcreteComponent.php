<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;

final class ConcreteComponent extends AbstractComponent
{
    /**
     * {@inheritDoc}
     *
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function getTemplateName(): string
    {
        return parent::getTemplateName();
    }
}
