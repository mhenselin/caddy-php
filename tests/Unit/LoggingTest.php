<?php

namespace Unit;

use mattvb91\CaddyPhp\Caddy;
use mattvb91\CaddyPhp\Config\Logging;
use mattvb91\CaddyPhp\Config\Logs\Log;
use mattvb91\CaddyPhp\Config\Logs\LogLevel;
use mattvb91\CaddyPhp\Config\Logs\Sampling;
use PHPUnit\Framework\TestCase;

class LoggingTest extends TestCase
{
    /**
     * @covers \mattvb91\CaddyPhp\Caddy::setLogging
     * @covers \mattvb91\CaddyPhp\Caddy::toArray
     */
    public function testAddingDefaultLog()
    {
        $caddy = new Caddy();
        $caddy->setLogging(
            (new Logging())
                ->addLog(new Log())
        );

        $this->assertEquals([
            'logs' => [
                'default' => [
                    'level' => LogLevel::DEBUG,
                ],
            ],
        ], $caddy->toArray()['logging']);
    }

    /**
     * @covers \mattvb91\CaddyPhp\Config\Logs\Log::__construct
     * @covers \mattvb91\CaddyPhp\Config\Logs\Sampling::toArray
     */
    public function testLoggingSampler()
    {
        $interval = random_int(0, 10);
        $first = random_int(0, 10);
        $thereAfter = random_int(0, 10);

        $caddy = new Caddy();
        $caddy->setLogging(
            (new Logging())
                ->addLog(
                    new Log(
                        sampling: new Sampling(
                            $interval,
                            $first,
                            $thereAfter
                        )
                    )
                )
        );

        $this->assertEquals([
            'interval'   => $interval,
            'first'      => $first,
            'thereafter' => $thereAfter,
        ], $caddy->toArray()['logging']['logs']['default']['sampling']);
    }
}
