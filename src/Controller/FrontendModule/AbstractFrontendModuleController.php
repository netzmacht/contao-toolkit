<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\FrontendModule;

use Contao\Model;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractModuleController is the base fragment controller for frontend modules
 *
 * @extends AbstractFragmentController<ModuleModel>
 */
abstract class AbstractFrontendModuleController extends AbstractFragmentController
{
    use ModuleRenderBackendViewTrait;

    /**
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The response tagger.
     * @param RouterInterface     $router           The router.
     * @param TranslatorInterface $translator       The translator.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        RouterInterface $router,
        TranslatorInterface $translator,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger);

        $this->router     = $router;
        $this->translator = $translator;
    }

    /**
     * Handle the fragment action for the frontend module.
     *
     * @param Request           $request The current request.
     * @param ModuleModel       $model   The module model.
     * @param string            $section The section in which the module is rendered.
     * @param list<string>|null $classes Additional css classes.
     */
    public function __invoke(
        Request $request,
        ModuleModel $model,
        string $section,
        array|null $classes = null,
    ): Response {
        if ($this->isBackendRequest($request)) {
            return $this->renderModuleBackendView($model);
        }

        return $this->generate($request, $model, $section, $classes);
    }

    #[Override]
    protected function getFallbackTemplateName(Model $model): string
    {
        return 'mod_' . $this->getType();
    }
}
