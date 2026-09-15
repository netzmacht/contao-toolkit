<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Closure;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractFrontendModuleController;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteFrontendModuleController extends AbstractFrontendModuleController
{
    public Closure|null $preGenerateCallback = null;
    public Closure|null $postGenerateCallback = null;

    public function callGetResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        return $this->getResponse($template, $model, $request);
    }

    #[Override]
    protected function preGenerate(FragmentTemplate $template, ModuleModel $model, Request $request): Response|null
    {
        return $this->preGenerateCallback
            ? ($this->preGenerateCallback)($template, $model, $request)
            : parent::preGenerate($template, $model, $request);
    }

    #[Override]
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ModuleModel $model,
        Request $request,
    ): Response|null {
        return $this->postGenerateCallback
            ? ($this->postGenerateCallback)($response, $template, $model, $request)
            : parent::postGenerate($response, $template, $model, $request);
    }
}
