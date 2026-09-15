<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

/**
 * Opt-in trait rendering the "###Wildcard###" backend placeholder for content elements that need
 * one (e.g. structural/side-effecting elements like sliders, accordions or forms). Not
 * auto-wired into Controller\Fragment\AbstractContentElementController — call it from your own
 * preGenerate() hook when $this->isBackendScope($request) is true.
 */
trait RenderBackendWildcardTrait
{
    protected function renderBackendWildcard(ContentModel $model, Request $request): Response
    {
        $name = $this->container->get('translator')->trans(
            sprintf('CTE.%s.0', $this->getType()),
            [],
            'contao_tl_content',
        );

        $href = $this->container->get('router')->generate(
            'contao_backend',
            ['do' => $request->query->get('do'), 'table' => 'tl_content', 'act' => 'edit', 'id' => $model->id],
        );

        return $this->render('@Contao/be_wildcard.html.twig', [
            'wildcard' => sprintf('###%s###', $name),
            'id' => $model->id,
            'link' => $name,
            'href' => $href,
        ]);
    }

    abstract protected function getType(): string;

    /** @return array<string,string> */
    public static function getSubscribedServices(): array
    {
        return [...parent::getSubscribedServices(), 'translator' => TranslatorInterface::class];
    }
}
