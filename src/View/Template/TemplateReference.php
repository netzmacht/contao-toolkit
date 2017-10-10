<?php

/**
 * Contao Toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\View\Template;

use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;

/**
 * Class TemplateReference
 *
 * @package Netzmacht\Contao\Toolkit\View\Template
 */
class TemplateReference extends BaseTemplateReference
{
    const SCOPE_BACKEND = 'be';

    const SCOPE_FRONTEND = 'fe';

    const ENGINE = 'toolkit';

    /**
     * TemplateReference constructor.
     *
     * @param string|null $name        The template name.
     * @param string|null $format      The template format.
     * @param string|null $scope       The template scope.
     * @param string      $contentType Content type.
     * @param string      $engine      Used engine.
     */
    public function __construct(
        $name = null,
        $format = null,
        $scope = null,
        $contentType = 'text/html',
        $engine = self::ENGINE
    ) {
        parent::__construct($name, $engine);

        $this->parameters['format']      = $format;
        $this->parameters['scope']       = $scope;
        $this->parameters['contentType'] = $contentType;
    }
}
