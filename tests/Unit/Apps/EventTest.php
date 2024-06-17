<?php

namespace Tests\Unit\Apps;

use mattvb91\CaddyPhp\Config\Apps\Events;
use Tests\TestCase;

class EventTest extends TestCase
{

    /**
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events::toArray
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events::__construct
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events\Subscription::toArray
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events\Subscription::__construct
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events\Handlers\Exec::toArray
     * @covers \mattvb91\CaddyPhp\Config\Apps\Events\Handlers\Exec::__construct
     */
    public function testAddingExecEvent()
    {
        $events = (new Events([
            (new Events\Subscription(
                handlers: [
                    new Events\Handlers\Exec(
                        "datetime",
                        [],
                        "/tmp",
                        0,
                        false,
                        []
                    ),
                ]
            )),
        ]));

        $this->assertEquals(
            [
                'subscriptions' => [
                    [
                        'events'   => [],
                        'modules'  => [],
                        'handlers' => [
                            [
                                'handler'     => 'exec',
                                'command'     => 'datetime',
                                'args'        => [],
                                'dir'         => '/tmp',
                                'timeout'     => 0,
                                'foreground'  => false,
                                'abort_codes' => [],
                            ],
                        ],
                    ],
                ],
            ],
            $events->toArray()
        );
    }

}