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

namespace Netzmacht\Contao\Toolkit\Controller\FrontendModule;

use Contao\Model;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractModuleController is the base fragment controller for frontend modules
 */
abstract class AbstractFrontendModuleController extends AbstractFragmentController
{
    use ModuleRenderBackendViewTrait;

    /**
     * Constructor.
     *
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
        TranslatorInterface $translator
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger);

        $this->router     = $router;
        $this->translator = $translator;
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
    public function __invoke(Request $request, ModuleModel $model, string $section, ?array $classes = null): Response
    {
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
        return 'mod_' . $this->getType();
    }
}
