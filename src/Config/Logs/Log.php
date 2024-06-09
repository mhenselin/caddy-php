<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Logs;

use mattvb91\CaddyPhp\Interfaces\Arrayable;

/**
 * Logs are your logs, keyed by an arbitrary name of your choosing.
 * The default log can be customized by defining a log called "default".
 * You can further define other logs and filter what kinds of entries they accept.
 *
 * https://caddyserver.com/docs/json/logging/logs/
 */
class Log implements Arrayable
{
    private LogLevel $level;
    private ?Sampling $sampling;

    public function __construct(
        LogLevel $level = LogLevel::DEBUG,
        Sampling $sampling = null
    ) {
        $this->level = $level;
        $this->sampling = $sampling;
    }

    public function getLevel(): LogLevel
    {
        return $this->level;
    }

    public function setSample(Sampling $sample): self
    {
        $this->sampling = $sample;

        return $this;
    }

    public function toArray(): array
    {
        $array = [
            'level' => $this->getLevel(),
        ];

        if (isset($this->sampling)) {
            $array['sampling'] = $this->sampling->toArray();
        }

        return $array;
    }
}
