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
     * @var Adapter<Input>
     */
    protected $inputAdapter;

    /**
     * Render backend view.
     *
     * @param ContentModel $model The content model.
     *
     * @return Response
     */
    protected function renderContentBackendView(ContentModel $model): Response
    {
        $name = $this->translator->trans(sprintf('CTE.%s.0', $this->getType()), [], 'contao_tl_content');
        $href = $this->router->generate(
            'contao_backend',
            ['do' => $this->inputAdapter->get('do'), 'table' => 'tl_content', 'act' => 'edit', 'id' => $model->id]
        );

        return $this->renderResponse(
            'toolkit:be:be_wildcard.html5',
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
     * @param string $templateName The template name.
     * @param array  $data         The data being passed to the template.
     *
     * @return Response
     */
    abstract protected function renderResponse(string $templateName, array $data): Response;

    /**
     * Get the type of the fragment.
     *
     * @return string
     */
    abstract protected function getType(): string;
}
