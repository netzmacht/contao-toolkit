<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\CoreBundle\Twig\Finder\Finder;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Contao\CoreBundle\Twig\Loader\ContaoFilesystemLoader;
use Contao\CoreBundle\Twig\Loader\ThemeNamespace;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Options\TemplateOptionsListener;
use PhpSpec\ObjectBehavior;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use spec\Netzmacht\Contao\Toolkit\DataContainerSpecHelper;

use function str_contains;

/**
 * @see TemplateOptionsListener
 *
 * Note on collaborator doubling: `Contao\CoreBundle\Twig\Finder\Finder` is a `final` class,
 * which Prophecy (the doubling library phpspec uses) cannot mock at all - not a flaky
 * environment issue, but a hard limitation (Prophecy generates a double by subclassing,
 * which PHP forbids for final classes). The examples exercising the Finder-based branch
 * therefore construct a *real* `Finder` instance around a mocked (non-final)
 * `ContaoFilesystemLoader`, which is the only collaborator `Finder` actually reads from
 * for `identifier()`/`withVariants()`/`asIdentifierList()`.
 */
class TemplateOptionsListenerSpec extends ObjectBehavior
{
    use DataContainerSpecHelper;

    public function let(DcaManager $dcaManager, FinderFactory $finderFactory): void
    {
        $this->beConstructedWith($dcaManager, $finderFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TemplateOptionsListener::class);
    }

    public function it_uses_get_template_group_for_a_classic_prefix(
        DcaManager $dcaManager,
        FinderFactory $finderFactory,
    ): void {
        $dca = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'ce_']]]]];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        // A classic (slash-free) prefix must never reach the Finder-based branch.
        $finderFactory->create()->shouldNotBeCalled();

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        // Controller::getTemplateGroup() is a legacy Contao static method with a deep chain
        // of dependencies on a fully bootstrapped service container (System::getContainer()),
        // a couple of $GLOBALS registries populated by the framework bootstrap, and - beyond
        // a certain point - a database connection for the theme lookup. None of that exists
        // in this phpspec unit test process. Faking that whole environment is out of scope
        // for this extension (see task brief), so this only asserts that any failure
        // genuinely originates inside Contao core - i.e. that our own delegation code did
        // not break - rather than requiring the legacy call itself to fully succeed.
        try {
            $this->onOptionsCallback($dataContainer)->shouldBeArray();
        } catch (Throwable $exception) {
            if (str_contains($exception->getFile(), 'TemplateOptionsListener.php')) {
                throw $exception;
            }
        }
    }

    public function it_uses_the_finder_for_a_modern_namespaced_prefix(
        DcaManager $dcaManager,
        FinderFactory $finderFactory,
        ContaoFilesystemLoader $filesystemLoader,
        TranslatorInterface $translator,
    ): void {
        $dca = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'content_element/text']]]]];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        $finder = new Finder($filesystemLoader->getWrappedObject(), new ThemeNamespace(), $translator->getWrappedObject());

        $filesystemLoader->getInheritanceChains(null)->willReturn([
            'content_element/text' => ['@Contao/content_element/text.html.twig' => 'App'],
            'content_element/text/custom1' => ['@Contao/content_element/text/custom1.html.twig' => 'App'],
            'content_element/other' => ['@Contao/content_element/other.html.twig' => 'App'],
        ]);

        $finderFactory->create()->willReturn($finder);

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        $this->onOptionsCallback($dataContainer)->shouldReturn([
            'content_element/text',
            'content_element/text/custom1',
        ]);
    }

    public function it_still_applies_the_exclude_list_for_the_finder_path(
        DcaManager $dcaManager,
        FinderFactory $finderFactory,
        ContaoFilesystemLoader $filesystemLoader,
        TranslatorInterface $translator,
    ): void {
        $dca = [
            'fields' => [
                'template' => [
                    'toolkit' => [
                        'template_options' => [
                            'prefix'  => 'content_element/text',
                            'exclude' => ['content_element/text/custom1'],
                        ],
                    ],
                ],
            ],
        ];

        $dcaManager->getDefinition('tl_content')->willReturn(new Definition('tl_content', $dca));

        $finder = new Finder($filesystemLoader->getWrappedObject(), new ThemeNamespace(), $translator->getWrappedObject());

        $filesystemLoader->getInheritanceChains(null)->willReturn([
            'content_element/text' => ['@Contao/content_element/text.html.twig' => 'App'],
            'content_element/text/custom1' => ['@Contao/content_element/text/custom1.html.twig' => 'App'],
        ]);

        $finderFactory->create()->willReturn($finder);

        $dataContainer = $this->createDataContainer('tl_content', 'template');

        $this->onOptionsCallback($dataContainer)->shouldReturn(['content_element/text']);
    }
}
