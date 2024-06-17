<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Apps\Events;

use mattvb91\CaddyPhp\Interfaces\Apps\Events\HandlerInterface;
use mattvb91\CaddyPhp\Interfaces\Arrayable;

class Subscription implements Arrayable
{
    /** @var string[] */
    private array $events;

    /** @var string [] */
    private array $modules;

    /** @var HandlerInterface[] */
    private array $handlers;

    /**
     * @param string[] $events
     * @param string[] $modules
     * @param HandlerInterface[] $handlers
     */
    public function __construct(
        array $events = [],
        array $modules = [],
        array $handlers = []
    ) {
        $this->events = $events;
        $this->modules = $modules;
        $this->handlers = $handlers;
    }

    public function toArray(): array
    {
        return [
            'events'   => $this->events,
            'modules'  => $this->modules,
            'handlers' => array_map(function (HandlerInterface $handler): array {
                return $handler->toArray();
            }, $this->handlers),
        ];
    }
}
