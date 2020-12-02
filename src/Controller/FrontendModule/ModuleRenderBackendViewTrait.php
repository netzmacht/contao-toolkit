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

use Contao\ModuleModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function sprintf;

/**
 * The RenderBackendViewTrait renders the backend placeholder view for modules
 */
trait ModuleRenderBackendViewTrait
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
     * Render backend view.
     *
     * @param ModuleModel $module The module model.
     *
     * @return Response
     */
    protected function renderModuleBackendView(ModuleModel $module): Response
    {
        $name = $this->translator->trans(sprintf('FMD.%s.0', $this->getType()), [], 'contao_modules');
        $href = $this->router->generate(
            'contao_backend',
            ['do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $module->id]
        );

        return $this->renderResponse(
            'toolkit:be:be_wildcard.html5',
            [
                'wildcard' => sprintf('###%s###', $name),
                'id'       => $module->id,
                'link'     => $module->name,
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
