<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Contao\ContentModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use Netzmacht\Contao\Toolkit\Controller\Fragment\RenderBackendWildcardTrait;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteRenderBackendWildcardController extends AbstractContentElementController
{
    use RenderBackendWildcardTrait;

    public function callRenderBackendWildcard(ContentModel $model, Request $request): Response
    {
        return $this->renderBackendWildcard($model, $request);
    }

    #[Override]
    protected function getType(): string
    {
        return 'text';
    }
}
