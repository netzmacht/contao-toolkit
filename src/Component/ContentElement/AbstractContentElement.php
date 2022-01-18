<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

use function defined;
use function time;
use function trigger_error;
use function trim;

use const BE_USER_LOGGED_IN;
use const E_USER_DEPRECATED;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedClass
 * @psalm-suppress DeprecatedInterface
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

            $isPreviewMode = defined('BE_USER_LOGGED_IN') && BE_USER_LOGGED_IN;
        }

        $this->isPreviewMode = $isPreviewMode;
    }

    /** @psalm-suppress DeprecatedClass */
    public function generate(): string
    {
        if (! $this->isVisible()) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Check if content element is visible.
     */
    protected function isVisible(): bool
    {
        if ($this->isPreviewMode || ! $this->isFrontendRequest()) {
            return true;
        }

        if ($this->get('invisible')) {
            return false;
        }

        $now   = time();
        $start = $this->get('start');
        $stop  = $this->get('stop');

        // phpcs:disable SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        return ($start == '' || $start <= $now) && ($stop == '' || $stop >= $now);
        // phpcs:enable SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
    }

    /** @psalm-suppress DeprecatedClass */
    protected function compileCssClass(): string
    {
        return trim('ce_' . $this->get('type') . ' ' . parent::compileCssClass());
    }
}
