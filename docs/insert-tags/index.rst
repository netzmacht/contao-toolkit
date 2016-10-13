Insert tags
===========

Toolkit introduces an object oriented way to implement own insert tags and also add an API to replace insert tags.

Replacing insert tags
---------------------

Why does Toolkit provides an own API entry point to replace insert tags? Looking at Contao's history the way how insert
tags could be replaced has changed some times. The method was protected for a while, got public static later and finally
got extracted into an own InsertTags class.

Supporting different Contao versions is a bit complicated here even then not extending every class form the *Controller*
class. That's why Toolkit v1 provided a standard way to replace insert tags.

.. code-block:: php

   <?php

    /** @var Netzmacht\Contao\Toolkit\InsertTag\Replacer $replacer */
    $replacer = $container->get(Netzmacht\Contao\Toolkit\DependencyInjection\Services::INSERT_TAG_REPLACER);
    $buffer   = $replacer->replace($buffer);

.. hint:: You should be careful using the InsertTags class provided by Contao. It hassles with the right order of the
Contao object stack.


Register a insert tag parser
----------------------------

The insert tag component of Toolkit allows you to provide a custom insert tag parser which do the replacing stuff for
you. The :code:`Netzmacht\Contao\Toolkit\InsertTag\Parser` interface is used for it.

.. code-block:: php

    // 1. Define your parser
    class SmileyParser implements Netzmacht\Contao\Toolkit\InsertTag\Parser
    {
        // Check if tag is supported. Note that not the whole insert tag is passed here, only the part before the first ::
        public function supports($tag)
        {
            return $tag === 'smiley';
        }

        // An insert tag value, e.g. smiley::lol?color=blue would be passed this way
        // $raw = 'smiley::lol?color=blue'
        // $tag = 'smiley'
        // $params = 'lol?color=blue' (All after the first ::)-
        public function parse($raw, $tag, $params = null, $cache = true)
        {
            // Do the parsing here.
        }
    }

    // 2. Register your parser

    // config/event_listeners.php

    return [
        Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent::Name => [
            function ($event) {
                $container = $event->getContainer();
                $replacer  = $container->get(Netzmacht\Contao\Toolkit\DependencyInjection\Services::INSERT_TAG_REPLACER);
                $parser    = $container->get('my.smiley.parser');

                $replacer->register($parser);
            }
        ]
    ];
