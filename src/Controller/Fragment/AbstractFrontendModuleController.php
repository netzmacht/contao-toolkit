<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

// phpcs:ignore Generic.Files.LineLength.MaxExceeded
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController as ContaoAbstractFrontendModuleController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Slim frontend module base controller on top of Contao Core's own fragment infrastructure.
 *
 * Backend-scope wildcard rendering is already handled automatically by
 * Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController::__invoke()
 * before getResponse() is even called — no isHidden()/wildcard logic is needed here.
 */
abstract class AbstractFrontendModuleController extends ContaoAbstractFrontendModuleController
{
    #[Override]
    final protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $response = $this->preGenerate($template, $model, $request);
        if ($response !== null) {
            return $response;
        }

        $template->setData($this->prepareTemplateData($template->getData(), $request, $model));
        $response = $template->getResponse();

        return $this->postGenerate($response, $template, $model, $request) ?? $response;
    }

    /**
     * Pre-generate hook. Return a Response to short-circuit the default rendering.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preGenerate(FragmentTemplate $template, ModuleModel $model, Request $request): Response|null
    {
        return null;
    }

    /**
     * Prepare the template data before rendering. Must return the (optionally modified) data.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareTemplateData(array $data, Request $request, ModuleModel $model): array
    {
        return $data;
    }

    /**
     * Post-generate hook. Return a Response to replace the default rendered response,
     * or null to keep it unchanged.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ModuleModel $model,
        Request $request,
    ): Response|null {
        return null;
    }
}
