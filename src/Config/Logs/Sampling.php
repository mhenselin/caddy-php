<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Logs;

use mattvb91\CaddyPhp\Interfaces\Arrayable;

/**
 * https://caddyserver.com/docs/json/logging/logs/sampling/
 */
class Sampling implements Arrayable
{
    private int $interval;
    private int $first;
    private int $thereafter;

    public function __construct(int $interval = 0, int $first = 0, int $thereafter = 0)
    {
        $this->interval = $interval;
        $this->first = $first;
        $this->thereafter = $thereafter;
    }

    public function toArray(): array
    {
        return [
            'interval'   => $this->interval,
            'first'      => $this->first,
            'thereafter' => $this->thereafter,
        ];
    }
}
