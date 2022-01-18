<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

/**
 * The RenderBackendViewTrait renders the backend placeholder view for modules
 */
trait RenderBackendViewTrait
{
    /**
     * The router.
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * The input adapter.
     *
     * @var Adapter<Input>|null
     */
    protected $inputAdapter;

    /**
     * Render backend view.
     *
     * @param ContentModel $model The content model.
     */
    protected function renderContentBackendView(ContentModel $model): Response
    {
        $module = $this->inputAdapter ? $this->inputAdapter->get('do') : Input::get('do');
        $name   = $this->translator->trans(sprintf('CTE.%s.0', $this->getType()), [], 'contao_tl_content');
        $href   = $this->router->generate(
            'contao_backend',
            ['do' => $module, 'table' => 'tl_content', 'act' => 'edit', 'id' => $model->id]
        );

        return $this->renderResponse(
            'be:be_wildcard',
            [
                'wildcard' => sprintf('###%s###', $name),
                'id'       => $model->id,
                'link'     => $name,
                'href'     => $href,
            ]
        );
    }

    /**
     * Render a response.
     *
     * The template name.
     *
     * @param string              $templateName The template name.
     * @param array<string,mixed> $data         The data being passed to the template.
     */
    abstract protected function renderResponse(string $templateName, array $data): Response;

    /**
     * Get the type of the fragment.
     */
    abstract protected function getType(): string;
}
