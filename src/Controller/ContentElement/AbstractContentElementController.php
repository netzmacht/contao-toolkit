<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractContentElementController is the base fragment controller for content elements
 *
 * @extends AbstractFragmentController<ContentModel>
 */
abstract class AbstractContentElementController extends AbstractFragmentController
{
    use IsHiddenTrait;

    /**
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The response tagger.
     * @param TokenChecker        $tokenChecker     The token checker.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger);

        $this->tokenChecker = $tokenChecker;
    }

    /**
     * Handle the fragment action for the content element.
     *
     * @param Request           $request The current request.
     * @param ContentModel      $model   The content model.
     * @param string            $section The section in which the content element is rendered.
     * @param list<string>|null $classes Additional css classes.
     */
    public function __invoke(Request $request, ContentModel $model, string $section, ?array $classes = null): Response
    {
        if ($this->isHidden($model, $request)) {
            return new Response();
        }

        return $this->generate($request, $model, $section, $classes);
    }

    protected function getFallbackTemplateName(Model $model): string
    {
        return 'ce_' . $this->getType();
    }
}
