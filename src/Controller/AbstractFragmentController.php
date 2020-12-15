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

namespace Netzmacht\Contao\Toolkit\Controller;

use Contao\CoreBundle\Fragment\FragmentOptionsAwareInterface;
use Contao\Model;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_unshift;
use function implode;
use function is_array;
use function ltrim;
use function strpos;
use function strrchr;
use function substr;

/**
 * This class a the base class for the base fragment controller provided by the Toolkit.
 */
abstract class AbstractFragmentController implements FragmentOptionsAwareInterface
{
    /**
     * Template renderer.
     *
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    protected $scopeMatcher;

    /**
     * The http request response tagger.
     *
     * @var ResponseTagger
     */
    private $responseTagger;

    /**
     * Fragment options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * AbstractFragmentController constructor.
     *
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The http request response tagger.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->scopeMatcher     = $scopeMatcher;
        $this->responseTagger   = $responseTagger;
    }

    /**
     * Set the fragment options.
     *
     * @param array $options The fragment options.
     *
     * @return void
     */
    public function setFragmentOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Generate the response for a fragment.
     *
     * This method executed the preGenerate() and postGenerate() method which might also return a response and intercept
     * the default rendering.
     *
     * @param Request    $request The given request.
     * @param Model      $model   The related model providing the configuration.
     * @param string     $section The section in which the fragment is rendered.
     * @param array|null $classes Additional classes.
     *
     * @return Response
     */
    protected function generate(Request $request, Model $model, string $section, ?array $classes = null): Response
    {
        $response = $this->preGenerate($request, $model, $section, $classes);
        if ($response !== null) {
            return $response;
        }

        $data     = $this->prepareDefaultTemplateData($model, $section, $classes);
        $data     = $this->prepareTemplateData($data, $request, $model);
        $buffer   = $this->render($this->getTemplateName($model), $data);
        $response = $this->postGenerate($buffer, $data, $request, $model);

        if ($response !== null) {
            return $response;
        }

        $this->tagResponse(sprintf('contao.db.%s.%s', $model::getTable(), $model->id));

        return new Response($buffer);
    }

    /**
     * Prepare the default template data.
     *
     * @param Model      $model   The related model providing the configuration.
     * @param string     $section The section in which the fragment is rendered.
     * @param array|null $classes Additional classes.
     *
     * @return array
     */
    private function prepareDefaultTemplateData(Model $model, string $section, ?array $classes = null): array
    {
        $data    = $model->row();
        $cssID   = StringUtil::deserialize($data['cssID'], true);
        $classes = $classes ?: [];

        if (!$cssID[1] !== '') {
            array_unshift($classes, $cssID[1]);
        }

        $data['inColumn'] = $section;
        $data['cssID']    = ($cssID[0] !== '') ? ' id="' . $cssID[0] . '"' : '';
        $data['class']    = trim($this->getTemplateName($model) . ' ' . implode(' ', $classes));

        $headline = StringUtil::deserialize($data['headline']);
        if (is_array($headline)) {
            $data['headline'] = $headline['value'];
            $data['hl']       = $headline['unit'];
        } else {
            $data['headline'] = $headline;
            $data['hl']       = 'h1';
        }

        return $data;
    }

    /**
     * Prepare the template data.
     *
     * The method has to extend the existing data and return the modified one as return value.
     *
     * @param array   $data    The parsed template data.
     * @param Request $request The current request.
     * @param Model   $model   The model containing the configuration.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        return $data;
    }

    /**
     * Pre generate is called first before any logic is called in the generate method.
     *
     * Use it to intercept the default behaviour.
     *
     * @param Request    $request The given request.
     * @param Model      $model   The related model providing the configuration.
     * @param string     $section The section in which the fragment is rendered.
     * @param array|null $classes Additional classes.
     *
     * @return Response|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preGenerate(Request $request, Model $model, string $section, ?array $classes = null): ?Response
    {
        return null;
    }

    /**
     * Post generate is called after the default rendering is done.
     *
     * Return a custom response if you want to modify the output.
     *
     * @param string  $buffer  The generated output.
     * @param array   $data    The parsed data.
     * @param Request $request The given request.
     * @param Model   $model   The related model providing the configuration.
     *
     * @return Response|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function postGenerate(string $buffer, array $data, Request $request, Model $model): ?Response
    {
        return null;
    }

    /**
     * Render a template and return the result as string.
     *
     * @param string $templateName The template name.
     * @param array  $data         The template data.
     *
     * @return string
     */
    protected function render(string $templateName, array $data): string
    {
        if (substr($templateName, -5) !== '.twig'
            && strpos($templateName, 'toolkit:') !== 0
            && strpos($templateName, 'fe:') !== 0
            && strpos($templateName, 'be:') !== 0
        ) {
            $templateName = 'fe:' . $templateName;
        }

        return $this->templateRenderer->render($templateName, $data);
    }

    /**
     * Render a template and return the result as response.
     *
     * @param string $templateName The template name.
     * @param array  $data         The template data.
     *
     * @return Response
     */
    protected function renderResponse(string $templateName, array $data): Response
    {
        return new Response($this->render($templateName, $data));
    }

    /**
     * Get the template name from the fragment options and or the provided model.
     *
     * @param Model $model The model containing the fragment configuration.
     *
     * @return string
     */
    protected function getTemplateName(Model $model): string
    {
        if ($model->customTpl) {
            return $model->customTpl;
        }

        if (isset($this->options['template'])) {
            return $this->options['template'];
        }

        return $this->getFallbackTemplateName($model);
    }

    /**
     * Get the type of the fragment.
     *
     * @return string
     */
    protected function getType(): string
    {
        if (isset($this->options['type'])) {
            return $this->options['type'];
        }

        $className = ltrim(strrchr(static::class, '\\'), '\\');

        if ('Controller' === substr($className, -10)) {
            $className = substr($className, 0, -10);
        }

        return Container::underscore($className);
    }

    /**
     * Check if the request is a backend request.
     *
     * @param Request $request The current request.
     *
     * @return bool
     */
    protected function isBackendRequest(Request $request): bool
    {
        return $this->scopeMatcher->isBackendRequest($request);
    }

    /**
     * Get the fallback template name.
     *
     * @param Model $model The model containing the fragment configuration.
     *
     * @return string
     */
    abstract protected function getFallbackTemplateName(Model $model): string;

    /**
     * Tag the current response.
     *
     * @param string ...$tags The list of tags.
     *
     * @return void
     */
    protected function tagResponse(string ...$tags): void
    {
        $this->responseTagger->addTags($tags);
    }
}
