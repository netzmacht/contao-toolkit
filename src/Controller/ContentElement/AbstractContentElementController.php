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
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Controller\AbstractFragmentController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractContentElementController is the base fragment controller for content elements
 */
abstract class AbstractContentElementController extends AbstractFragmentController
{
    use IsHiddenTrait;

    /**
     * Constructor.
     *
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The response tagger.
     * @param TokenChecker        $tokenChecker     The token checker.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger);

        $this->tokenChecker = $tokenChecker;
    }

    /**
     * Handle the fragment action for the content element.
     *
     * @param Request      $request The current request.
     * @param ContentModel $model   The content model.
     * @param string       $section The section in which the content element is rendered.
     * @param array|null   $classes Additional css classes.
     *
     * @return Response
     */
    public function __invoke(Request $request, ContentModel $model, string $section, ?array $classes = null): Response
    {
        if ($this->isHidden($model, $request)) {
            return new Response();
        }

        return $this->generate($request, $model, $section, $classes);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFallbackTemplateName(Model $model): string
    {
        return 'ce_' . $this->getType();
    }
}
