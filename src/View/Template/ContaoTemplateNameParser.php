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

use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * Class ContaoTemplateNameParser.
 *
 * @package Netzmacht\Contao\Toolkit\View\Template
 */
class ContaoTemplateNameParser implements TemplateNameParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if (preg_match('/^([^\:]{1,})\:(.{1,})\.([^\.]{1,})$/', $name, $matches)) {
            $this->guardSupportedScope($matches[1]);

            return new TemplateReference($matches[2], $matches[1], $matches[0]);
        }

        throw new \RuntimeException(
            sprintf('Could not parse template name "%s". Expected format is "scope:name.format"', $name)
        );
    }

    /**
     * Guard that template scope is supported.
     *
     * @param string $scope Given scope.
     *
     * @throws \RuntimeException when template scope is not supported.
     */
    private function guardSupportedScope($scope): void
    {
        if ($scope !== TemplateReference::SCOPE_FRONTEND
            && $scope !== TemplateReference::SCOPE_BACKEND) {
            throw new \RuntimeException(
                sprintf(
                    'Template scope "%s" is not supported. Has to be contao_frontend or contao_backend.',
                    $scope
                )
            );
        }
    }
}
