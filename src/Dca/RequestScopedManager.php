<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Symfony\Component\HttpFoundation\RequestStack;

use function spl_object_id;

final class RequestScopedManager implements DcaManager
{
    /** @var array<string, DcaManager> */
    private array $managers = [];

    public function __construct(
        private readonly DcaLoader $loader,
        private readonly FormatterFactory $formatterFactory,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getDefinition(string $name, bool $noCache = false): Definition
    {
        return $this->getManager()->getDefinition($name, $noCache);
    }

    public function getFormatter(string $name): Formatter
    {
        return $this->getManager()->getFormatter($name);
    }

    private function getManager(): DcaManager
    {
        $request   = $this->requestStack->getCurrentRequest();
        $requestId = $request ? spl_object_id($request) : '__empty__';

        $this->managers[$requestId] ??= new Manager($this->loader, $this->formatterFactory);

        return $this->managers[$requestId];
    }
}
