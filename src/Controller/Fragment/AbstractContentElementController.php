<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
// phpcs:ignore Generic.Files.LineLength.MaxExceeded
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController as ContaoAbstractContentElementController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Slim content element base controller on top of Contao Core's own fragment infrastructure.
 *
 * Combines isHidden(), preGenerate(), the template-data hook and postGenerate() in the fixed
 * order below, mirroring the (now deprecated) Controller\ContentElement\AbstractContentElementController
 * hook names to ease migration.
 */
abstract class AbstractContentElementController extends ContaoAbstractContentElementController
{
    use IsHiddenTrait;

    #[Override]
    final protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->isHidden($model, $request)) {
            return new Response();
        }

        $response = $this->preGenerate($template, $model, $request);
        if ($response !== null) {
            return $response;
        }

        $template->setData($this->prepareTemplateData($template->getData(), $request, $model));
        $response = $template->getResponse();

        return $this->postGenerate($response, $template, $model, $request) ?? $response;
    }

    /**
     * Pre-generate hook. Return a Response to short-circuit the default rendering
     * (e.g. a redirect or a file download instead of the normal template).
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
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
    protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
    {
        return $data;
    }

    /**
     * Post-generate hook. Return a Response to replace the default rendered response
     * (e.g. to set additional cache-control directives), or null to keep it unchanged.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ContentModel $model,
        Request $request,
    ): Response|null {
        return null;
    }
}
