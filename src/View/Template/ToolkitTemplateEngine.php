<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Contao\Controller;
use RuntimeException;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Throwable;

use function sprintf;

class ToolkitTemplateEngine implements EngineInterface
{
    /**
     * Template factory.
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * Template name parser.
     *
     * @var TemplateNameParserInterface
     */
    private $nameParser;

    /**
     * @param TemplateFactory             $templateFactory Template factory.
     * @param TemplateNameParserInterface $nameParser      Template name parser.
     */
    public function __construct(
        TemplateFactory $templateFactory,
        TemplateNameParserInterface $nameParser
    ) {
        $this->templateFactory = $templateFactory;
        $this->nameParser      = $nameParser;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If template scope is not supported.
     */
    public function render($name, array $parameters = [])
    {
        $reference = $this->getTemplateReference($name);

        switch ($reference->get('scope')) {
            case TemplateReference::SCOPE_BACKEND:
                $template = $this->templateFactory->createBackendTemplate(
                    $reference->getLogicalName(),
                    $parameters,
                    $reference->get('contentType') ?: 'text/html'
                );
                break;

            case TemplateReference::SCOPE_FRONTEND:
                $template = $this->templateFactory->createFrontendTemplate(
                    $reference->getLogicalName(),
                    $parameters,
                    $reference->get('contentType') ?: 'text/html'
                );
                break;

            default:
                throw new RuntimeException(
                    sprintf(
                        'Rendering template "%s" failed. Template scope "%s" is not supported.',
                        $reference->getLogicalName(),
                        $reference->get('scope')
                    )
                );
        }

        return $template->parse();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        try {
            $reference = $this->getTemplateReference($name);
            Controller::getTemplate($reference->getLogicalName());

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        try {
            $reference = $this->getTemplateReference($name);
        } catch (Throwable $e) {
            return false;
        }

        return $reference->get('engine') === TemplateReference::ENGINE;
    }

    /**
     * Get the template reference.
     *
     * @param TemplateReferenceInterface|string $name Given template reference.
     *
     * @throws RuntimeException If template name is not supported.
     */
    private function getTemplateReference($name): TemplateReferenceInterface
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        return $this->nameParser->parse($name);
    }
}
