<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
class TemplateFactory
{
    use ServiceContainerTrait;

    const BACKEND = '\BackendTemplate';

    const FRONTEND = '\FrontendTemplate';

    /**
     * The template class.
     *
     * @var string
     */
    private $class = self::FRONTEND;

    /**
     * The template data.
     *
     * @var array
     */
    private $data = array();

    /**
     * The template name.
     *
     * @var string
     */
    private $name;

    /**
     * Construct.
     *
     * @param string $name The template name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Create the factory for a backend template.
     *
     * @param string $name The template name.
     *
     * @return TemplateFactory
     */
    public static function backendTemplate($name)
    {
        $factory = new self($name);
        $factory->withClass(static::BACKEND);

        return $factory;
    }

    /**
     * Create the factory for a frontend template.
     *
     * @param string $name The template name.
     *
     * @return TemplateFactory
     */
    public static function frontendTemplate($name)
    {
        $factory = new self($name);
        $factory->withClass(static::FRONTEND);

        return $factory;
    }

    /**
     * Create template with a specific class.
     *
     * @param string $class The class name.
     *
     * @return $this
     */
    public function withClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Add translator helper.
     *
     * @param string|null                     $defaultDomain The default domain.
     * @param string|null|TranslatorInterface $translator    The translator as object or service container id.
     *
     * @return $this
     */
    public function withTranslator($defaultDomain = null, $translator = null)
    {
        if (!$translator instanceof TranslatorInterface) {
            if ($translator) {
                $translator = $this->getService($translator);
            } else {
                $translator = new TranslatorChain();
                $translator->add(new LangArrayTranslator($this->getService('event-dispatcher')));
            }
        }

        $this->data['translate'] = function (
            $string,
            array $params = array(),
            $domain = null,
            $locale = null
        ) use (
            $defaultDomain,
            $translator
        ) {
            $domain = $domain ?: $defaultDomain;

            return $translator->translate($string, $domain, $params, $locale);

        };

        return $this;
    }

    /**
     * Add default data.
     *
     * @param array $defaults The default values.
     *
     * @return $this
     */
    public function withDefaults(array $defaults)
    {
        $this->data = array_merge($this->data, $defaults);


        return $this;
    }

    /**
     * Create the template.
     *
     * @return \Template
     */
    public function create()
    {
        $className = $this->class;

        /** @var \Template $template */
        $template = new $className($this->name);
        $template->setData($this->data);

        return $template;
    }
}
