<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Module;

use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function sprintf;
use function trim;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedClass
 * @psalm-suppress DeprecatedInterface
 */
abstract class AbstractModule extends AbstractComponent implements Module
{
    /**
     * Should the module be rendered in backend mode.
     *
     * @var bool
     */
    protected $renderInBackendMode = false;

    /**
     * Translator.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * @param Model|Collection|Result  $model               Object model or result.
     * @param TemplateEngine           $templateEngine      Template engine.
     * @param Translator               $translator          Translator.
     * @param string                   $column              Column.
     * @param RequestScopeMatcher|null $requestScopeMatcher Request scope matcher.
     *
     * @psalm-suppress DeprecatedClass
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        Translator $translator,
        $column = 'main',
        ?RequestScopeMatcher $requestScopeMatcher = null
    ) {
        parent::__construct($model, $templateEngine, $column, $requestScopeMatcher);

        $this->translator = $translator;
    }

    public function generate(): string
    {
        if (! $this->renderInBackendMode && $this->isBackendRequest()) {
            return $this->generateBackendView();
        }

        return parent::generate();
    }

    /**
     * Generate the backend view.
     */
    protected function generateBackendView(): string
    {
        $wildcard = sprintf(
            '### %s ###',
            $this->getTranslator()->trans(sprintf('FMD.%s.0', $this->get('type')), [], 'contao_modules')
        );

        $parameters = [
            'wildcard' => $wildcard,
            'title'    => $this->get('headline'),
            'id'       => $this->get('id'),
            'link'     => $this->get('name'),
            'href'     => $this->generateBackendLink(),
        ];

        return $this->render('toolkit:be:be_wildcard.html5', $parameters);
    }

    /**
     * Generate the backend link.
     */
    protected function generateBackendLink(): string
    {
        return 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->get('id');
    }

    /**
     * Get translator.
     */
    protected function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function compileCssClass(): string
    {
        return trim('mod_' . $this->get('type') . ' ' . parent::compileCssClass());
    }
}
