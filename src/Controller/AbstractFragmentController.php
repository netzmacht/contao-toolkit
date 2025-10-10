<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Controller;

use Contao\CoreBundle\Fragment\FragmentOptionsAwareInterface;
use Contao\Model;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_pad;
use function array_unshift;
use function array_values;
use function implode;
use function is_array;
use function ltrim;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function strrchr;
use function substr;
use function trim;

/**
 * This class a the base class for the base fragment controller provided by the Toolkit.
 *
 * @template TModel of Model
 */
abstract class AbstractFragmentController implements FragmentOptionsAwareInterface
{
    /**
     * Template renderer.
     */
    private TemplateRenderer $templateRenderer;

    /**
     * Request scope matcher.
     */
    protected RequestScopeMatcher $scopeMatcher;

    /**
     * The http request response tagger.
     */
    private ResponseTagger $responseTagger;

    /**
     * Fragment options.
     *
     * @var array<string,mixed>
     */
    protected array $options = [];

    /**
     * @param TemplateRenderer    $templateRenderer The template renderer.
     * @param RequestScopeMatcher $scopeMatcher     The scope matcher.
     * @param ResponseTagger      $responseTagger   The http request response tagger.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->scopeMatcher     = $scopeMatcher;
        $this->responseTagger   = $responseTagger;
    }

    /**
     * Set the fragment options.
     *
     * @param array<string,mixed> $options The fragment options.
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    #[Override]
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
     * @param Request           $request The given request.
     * @param Model             $model   The related model providing the configuration.
     * @param string            $section The section in which the fragment is rendered.
     * @param list<string>|null $classes Additional classes.
     * @psalm-param TModel $model
     */
    protected function generate(Request $request, Model $model, string $section, array|null $classes = null): Response
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
     * @param Model             $model   The related model providing the configuration.
     * @param string            $section The section in which the fragment is rendered.
     * @param list<string>|null $classes Additional classes.
     * @psalm-param TModel $model
     *
     * @return array<string,mixed>
     */
    private function prepareDefaultTemplateData(Model $model, string $section, array|null $classes = null): array
    {
        $data      = $model->row();
        $cssID     = array_pad(StringUtil::deserialize($data['cssID'], true), 2, '');
        $classes ??= [];

        if ($cssID[1] !== '') {
            array_unshift($classes, $cssID[1]);
        }

        $data['inColumn'] = $section;
        $data['cssID']    = $cssID[0] !== '' ? ' id="' . $cssID[0] . '"' : '';
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
     * @param array<string,mixed> $data    The parsed template data.
     * @param Request             $request The current request.
     * @param Model               $model   The model containing the configuration.
     * @psalm-param TModel $model
     *
     * @return array<string,mixed>
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
     * @param Request           $request The given request.
     * @param Model             $model   The related model providing the configuration.
     * @param string            $section The section in which the fragment is rendered.
     * @param list<string>|null $classes Additional classes.
     * @psalm-param TModel $model
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
        return null;
    }

    /**
     * Post generate is called after the default rendering is done.
     *
     * Return a custom response if you want to modify the output.
     *
     * @param string              $buffer  The generated output.
     * @param array<string,mixed> $data    The parsed data.
     * @param Request             $request The given request.
     * @param Model               $model   The related model providing the configuration.
     * @psalm-param TModel $model
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function postGenerate(string $buffer, array $data, Request $request, Model $model): Response|null
    {
        return null;
    }

    /**
     * Render a template and return the result as string.
     *
     * @param string              $templateName The template name.
     * @param array<string,mixed> $data         The template data.
     */
    protected function render(string $templateName, array $data): string
    {
        if (
            ! str_ends_with($templateName, '.twig')
            && ! str_starts_with($templateName, 'toolkit:')
            && ! str_starts_with($templateName, 'fe:')
            && ! str_starts_with($templateName, 'be:')
        ) {
            $templateName = 'fe:' . $templateName;
        }

        return $this->templateRenderer->render($templateName, $data);
    }

    /**
     * Render a template and return the result as response.
     *
     * @param string              $templateName The template name.
     * @param array<string,mixed> $data         The template data.
     */
    protected function renderResponse(string $templateName, array $data): Response
    {
        return new Response($this->render($templateName, $data));
    }

    /**
     * Get the template name from the fragment options and or the provided model.
     *
     * @param Model $model The model containing the fragment configuration.
     * @psalm-param TModel $model
     */
    protected function getTemplateName(Model $model): string
    {
        if ($model->customTpl && ! $this->scopeMatcher->isBackendRequest()) {
            return $model->customTpl;
        }

        if (isset($this->options['template'])) {
            return $this->options['template'];
        }

        return $this->getFallbackTemplateName($model);
    }

    /**
     * Get the type of the fragment.
     */
    protected function getType(): string
    {
        if (isset($this->options['type'])) {
            return $this->options['type'];
        }

        $className = ltrim((string) strrchr(static::class, '\\'), '\\');

        if (substr($className, -10) === 'Controller') {
            $className = substr($className, 0, -10);
        }

        return Container::underscore($className);
    }

    /**
     * Check if the request is a backend request.
     *
     * @param Request $request The current request.
     */
    protected function isBackendRequest(Request $request): bool
    {
        return $this->scopeMatcher->isBackendRequest($request);
    }

    /**
     * Get the fallback template name.
     *
     * @param Model $model The model containing the fragment configuration.
     * @psalm-param TModel $model
     */
    abstract protected function getFallbackTemplateName(Model $model): string;

    /**
     * Tag the current response.
     *
     * @param string ...$tags The list of tags.
     */
    protected function tagResponse(string ...$tags): void
    {
        $this->responseTagger->addTags(array_values($tags));
    }
}
