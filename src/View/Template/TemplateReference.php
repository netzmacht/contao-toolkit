<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;

/**
 * @deprecated Since 3.6.0 and get removed in 4.0.0
 */
class TemplateReference extends BaseTemplateReference
{
    public const SCOPE_BACKEND = 'be';

    public const SCOPE_FRONTEND = 'fe';

    public const ENGINE = 'toolkit';

    /**
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
