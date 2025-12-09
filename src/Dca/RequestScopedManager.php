<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WeakMap;

final class RequestScopedManager implements DcaManager
{
    /** @var WeakMap<Request|RequestScopedManager, DcaManager>  */
    private WeakMap $managers;

    public function __construct(
        private readonly DcaLoader $loader,
        private readonly FormatterFactory $formatterFactory,
        private readonly RequestStack $requestStack,
    ) {
        /** @psalm-var  WeakMap<Request|RequestScopedManager, DcaManager> $manager */
        $manager        = new WeakMap();
        $this->managers = $manager;
    }

    #[Override]
    public function getDefinition(string $name, bool $noCache = false): Definition
    {
        return $this->getManager()->getDefinition($name, $noCache);
    }

    #[Override]
    public function getFormatter(string $name): Formatter
    {
        return $this->getManager()->getFormatter($name);
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    private function getManager(): DcaManager
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            $request = $this;
        }

        if (! $this->managers->offsetExists($request)) {
            $this->managers->offsetSet($request, new Manager($this->loader, $this->formatterFactory));
        }

        return $this->managers->offsetGet($request);
    }
}
