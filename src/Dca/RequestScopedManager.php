<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestScopedManager implements DcaManager
{
    public function __construct(
        private readonly DcaLoader $loader,
        private readonly FormatterFactory $formatterFactory,
        private readonly RequestStack $requestStack,
    ) {
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

    private function getManager(): DcaManager
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return new Manager($this->loader, $this->formatterFactory);
        }

        $manager = $request->attributes->get(DcaManager::class);
        if ($manager instanceof DcaManager) {
            return $manager;
        }

        $manager = new Manager($this->loader, $this->formatterFactory);
        $request->attributes->set(DcaManager::class, $manager);

        return $manager;
    }
}
