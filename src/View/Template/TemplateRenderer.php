<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     Christopher Bölter <christopher@boelter.eu>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

interface TemplateRenderer
{
    public function render($name, array $parameters = []);
}
