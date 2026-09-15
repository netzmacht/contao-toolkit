<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Controller\Fragment;

use Closure;
use Contao\ContentModel;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Netzmacht\Contao\Toolkit\Controller\Fragment\AbstractContentElementController;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConcreteContentElementController extends AbstractContentElementController
{
    public Closure|null $preGenerateCallback = null;
    public Closure|null $prepareTemplateDataCallback = null;
    public Closure|null $postGenerateCallback = null;

    public function callGetResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        return $this->getResponse($template, $model, $request);
    }

    #[Override]
    protected function preGenerate(FragmentTemplate $template, ContentModel $model, Request $request): Response|null
    {
        return $this->preGenerateCallback
            ? ($this->preGenerateCallback)($template, $model, $request)
            : parent::preGenerate($template, $model, $request);
    }

    #[Override]
    protected function prepareTemplateData(array $data, Request $request, ContentModel $model): array
    {
        return $this->prepareTemplateDataCallback
            ? ($this->prepareTemplateDataCallback)($data, $request, $model)
            : parent::prepareTemplateData($data, $request, $model);
    }

    #[Override]
    protected function postGenerate(
        Response $response,
        FragmentTemplate $template,
        ContentModel $model,
        Request $request,
    ): Response|null {
        return $this->postGenerateCallback
            ? ($this->postGenerateCallback)($response, $template, $model, $request)
            : parent::postGenerate($response, $template, $model, $request);
    }
}
