<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Apps\Events\Handlers;

use mattvb91\CaddyPhp\Interfaces\Apps\Events\HandlerInterface;

/**
 * https://caddyserver.com/docs/json/apps/events/subscriptions/handlers/exec/
 */
class Exec implements HandlerInterface
{
    private string $command;

    /** @var string[]  */
    private array $args;

    private string $dir;

    private int $timeout;

    private bool $foreground;

    /** @var int[] */
    private array $abort_codes;

    /**
     * @param string[] $args
     * @param int[] $abort_codes
     */
    public function __construct(
        string $command,
        array $args,
        string $dir,
        int $timeout = 0,
        bool $foreground = false,
        array $abort_codes = [0]
    ) {
        $this->command = $command;
        $this->args = $args;
        $this->dir = $dir;
        $this->timeout = $timeout;
        $this->foreground = $foreground;
        $this->abort_codes = $abort_codes;
    }

    public function toArray(): array
    {
        return [
            'handler'     => $this->getHandler(),
            "command"     => $this->command,
            "args"        => $this->args,
            "dir"         => $this->dir,
            "timeout"     => $this->timeout,
            "foreground"  => $this->foreground,
            "abort_codes" => $this->abort_codes,
        ];
    }

    public function getHandler(): string
    {
        return 'exec';
    }
}
