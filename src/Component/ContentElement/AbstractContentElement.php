<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Dennis Bohn <dennis.bohn@bohn.media>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use function trigger_error;
use const BE_USER_LOGGED_IN;
use const E_USER_DEPRECATED;

/**
 * Class AbstractContentElement.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
abstract class AbstractContentElement extends AbstractComponent implements ContentElement
{
    /**
     * State if the request is called in the preview mode.
     *
     * @var bool|null
     */
    private $isPreviewMode;

    /**
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result  $model               Object model or result.
     * @param TemplateEngine           $templateEngine      Template engine.
     * @param string                   $column              Column.
     * @param RequestScopeMatcher|null $requestScopeMatcher Request scope matcher.
     * @param bool|null                $isPreviewMode       State if the request is called in the preview mode.
     *
     * @throws InvalidArgumentException When model does not have a row method.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        $column = 'main',
        ?RequestScopeMatcher $requestScopeMatcher = null,
        ?bool $isPreviewMode = null
    ) {
        parent::__construct($model, $templateEngine, $column, $requestScopeMatcher);

        if ($isPreviewMode === null) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'isPreviewMode not passed as fifth argument. isPreviewMode will be required in version 4.0.0',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd

            $isPreviewMode = BE_USER_LOGGED_IN;
        }

        $this->isPreviewMode = $isPreviewMode;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(): string
    {
        if (!$this->isVisible()) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Check if content element is visible.
     *
     * @return bool
     */
    protected function isVisible(): bool
    {
        if ($this->isPreviewMode || !$this->isFrontendRequest()) {
            return true;
        }

        if ($this->get('invisible')) {
            return false;
        }

        $now   = time();
        $start = $this->get('start');
        $stop  = $this->get('stop');

        if (($start != '' && $start > $now) || ($stop != '' && $stop < $now)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function compileCssClass(): string
    {
        return trim('ce_' . $this->get('type') . ' ' . parent::compileCssClass());
    }
}
