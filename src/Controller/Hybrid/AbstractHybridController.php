<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Hybrid;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\IsHiddenTrait;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\RenderBackendViewTrait as ContentRenderBackendViewTrait;
use Netzmacht\Contao\Toolkit\Controller\FrontendModule\ModuleRenderBackendViewTrait;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractHybridController is a base controller for hybrid fragment controllers.
 *
 * Hybrid fragment controllers might be used as frontend modules or content elements. Be aware that you have to register
 * the methods renderAsContentElement() and renderAsFrontendModule() as fragment controllers.
 */
abstract class AbstractHybridController extends AbstractFragmentController
{
    use IsHiddenTrait;
    use ModuleRenderBackendViewTrait;
    use ContentRenderBackendViewTrait;

    /**
     * Constructor.
     *
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The response tagger.
     * @param RouterInterface     $router           The router.
     * @param TranslatorInterface $translator       The translator.
     * @param TokenChecker        $tokenChecker     The token checker.
     * @param Adapter             $inputAdapter     The input adapter.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        TranslatorInterface $translator,
        TokenChecker $tokenChecker,
        Adapter $inputAdapter
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger);

        $this->router       = $router;
        $this->translator   = $translator;
        $this->tokenChecker = $tokenChecker;
        $this->inputAdapter = $inputAdapter;
    }

    /**
     * Handle the fragment action for the content element.
     *
     * @param Request      $request The current request.
     * @param ContentModel $model   The content model.
     * @param string       $section The section in which the content element is rendered.
     * @param array|null   $classes Additional css classes.
     *
     * @return Response
     */
    public function renderAsContentElement(
        Request $request,
        ContentModel $model,
        string $section,
        ?array $classes = null
    ): Response {
        if ($this->isHidden($model, $request)) {
            return new Response();
        }

        if ($this->isBackendRequest($request)) {
            return $this->renderContentBackendView($model);
        }

        return $this->generate($request, $model, $section, $classes);
    }

    /**
     * Handle the fragment action for the frontend module.
     *
     * @param Request     $request The current request.
     * @param ModuleModel $model   The module model.
     * @param string      $section The section in which the module is rendered.
     * @param array|null  $classes Additional css classes.
     *
     * @return Response
     */
    public function renderAsFrontendModule(
        Request $request,
        ModuleModel $model,
        string $section,
        ?array $classes = null
    ): Response {
        if ($this->isBackendRequest($request)) {
            return $this->renderModuleBackendView($model);
        }

        return $this->generate($request, $model, $section, $classes);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFallbackTemplateName(Model $model): string
    {
        if ($model instanceof ContentModel) {
            return 'ce_' . $this->getType();
        }

        return 'mod_' . $this->getType();
    }
}
