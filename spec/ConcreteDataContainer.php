<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use Contao\DataContainer;
use Override;

final class ConcreteDataContainer extends DataContainer
{
    #[Override]
    public function getPalette()
    {
        return '';
    }

    #[Override]
    protected function save($varValue)
    {
        return $varValue;
    }
}
