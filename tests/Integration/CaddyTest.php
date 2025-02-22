<?php

namespace Integration;

use GuzzleHttp\Client;
use mattvb91\CaddyPhp\Caddy;
use mattvb91\CaddyPhp\Config\Apps\Events;
use mattvb91\CaddyPhp\Config\Apps\Http;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Route;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Handle\Authentication;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Handle\Authentication\Providers\HttpBasic;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Handle\Authentication\Providers\HttpBasic\Account;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Handle\StaticResponse;
use mattvb91\CaddyPhp\Config\Apps\Http\Server\Routes\Match\Host;
use mattvb91\CaddyPhp\Config\Apps\Tls;
use mattvb91\CaddyPhp\Config\Logging;
use mattvb91\CaddyPhp\Config\Logs\Log;
use mattvb91\CaddyPhp\Config\Logs\Sampling;
use PHPUnit\Framework\TestCase;

class CaddyTest extends TestCase
{
    /**
     * @covers \mattvb91\CaddyPhp\Caddy::load
     */
    public function testCanLoadConfig(): void
    {
        $caddy = new Caddy();

        $this->assertTrue($caddy->load());
    }

    /**
     * @coversNothing
     */
    public function testCanLoadWithLogs(): void
    {
        $caddy = new Caddy();
        $caddy->setLogging(
            (new Logging())
                ->addLog(new Log(sampling: new Sampling()))
        );

        $this->assertTrue($caddy->load());
    }

    /**
     * @coversNothing
     */
    public function testCanLoadWithHttpApp(): void
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())
                )
            )
        );

        $this->assertTrue($caddy->load());
    }

    /**
     * @coversNothing
     */
    public function testCanLoadWithEventsApp()
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Events([
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
            ]))
        );

        $this->assertTrue(
            $caddy->load()
        );
    }

    /**
     * @coversNothing
     */
    public function testCanLoadStaticResponseApp(): void
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())->addHandle(
                        new StaticResponse('phpunit', 200)
                    )
                )
            )
        );

        $this->assertTrue($caddy->load());

        $client = new Client([
            'base_uri' => 'caddy',
        ]);

        $request = $client->get('');

        $this->assertEquals(200, $request->getStatusCode());
        $this->assertEquals('phpunit', $request->getBody());
    }

    /**
     * @covers \mattvb91\CaddyPhp\Caddy::addHostname
     * @covers \mattvb91\CaddyPhp\Caddy::removeHostname
     * @covers \mattvb91\CaddyPhp\findHost
     */
    public function testCanAddRemoveHosts()
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())->addHandle(
                        new StaticResponse('host test', 200)
                    )->addMatch(
                        (new Host('main'))
                            ->setHosts(['test.localhost'])
                    )
                )->addRoute(
                    (new Route())
                        ->addHandle(new StaticResponse('Not found', 404))
                        ->addMatch(
                            (new Host('notFound'))
                                ->setHosts(['*.localhost'])
                        )
                )
            )
        );

        $this->assertTrue($caddy->load());
        $this->assertTrue($caddy->addHostname('main', 'new.localhost'));

        $client = new Client([
            'base_uri' => 'caddy',
            'headers'  => [
                'Host' => 'new.localhost',
            ],
        ]);

        $request = $client->get('');
        $this->assertEquals(200, $request->getStatusCode());

        $client = new Client([
            'base_uri' => 'caddy',
            'headers'  => [
                'Host' => 'test.localhost',
            ],
        ]);

        $request = $client->get('');
        $this->assertEquals(200, $request->getStatusCode());

        $client = new Client([
            'base_uri'    => 'caddy',
            'http_errors' => false,
            'headers'     => [
                'Host' => 'notfound.localhost',
            ],
        ]);

        $request = $client->get('');
        $this->assertEquals(404, $request->getStatusCode());

        $caddy->removeHostname('main', 'test.localhost');

        $client = new Client([
            'base_uri'    => 'caddy',
            'http_errors' => false,
            'headers'     => [
                'Host' => 'test.localhost',
            ],
        ]);

        $request = $client->get('');
        $this->assertEquals(404, $request->getStatusCode());
    }

    /**
     * @covers \mattvb91\CaddyPhp\Caddy::syncHosts
     */
    public function testSyncHostsWorks()
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())->addHandle(
                        new StaticResponse('host test', 200)
                    )->addMatch(
                        (new Host('main'))
                            ->setHosts([
                                'test.localhost',
                                'test2.localhost',
                                'localhost',
                            ])
                    )
                )
            )
        );
        $caddy->load();

        //Create instance without setting hosts
        $caddy = new Caddy();
        $mainHost = new Host('main');
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())->addHandle(
                        new StaticResponse('host test', 200)
                    )->addMatch($mainHost)
                )
            )
        );

        $caddy->syncHosts('main');
        $this->assertCount(3, $mainHost->getHosts());
    }

    /**
     * @coversNothing
     */
    public function testHttpBasicAuth()
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())->addServer(
                'server1',
                (new Http\Server())->addRoute(
                    (new Route())
                        ->addHandle(
                            (new Authentication())
                                ->addProvider(
                                    (new HttpBasic())
                                        ->addAccount(new Account('test', 'test123'))
                                )
                        )
                        ->addHandle(
                            new StaticResponse('auth test', 200)
                        )->addMatch(
                            (new Host('main'))
                                ->setHosts([
                                    'localhost',
                                ])
                        )
                )
            )
        );
        $caddy->load();

        $client = new Client([
            'base_uri'    => 'caddy',
            'http_errors' => false,
            'headers'     => [
                'Host' => 'localhost',
            ],
        ]);

        $request = $client->get('');
        $this->assertEquals(401, $request->getStatusCode());

        $request = $client->request('GET', '', [
            'auth' => [
                'test',
                'test123',
            ],
        ]);
        $this->assertEquals(200, $request->getStatusCode());
    }

    /**
     * @coversNothing
     *
     * This works but causes a panic serve and crashes the caddy server during multiple test runs.
     * Need to investigate
     */
//    public function test_can_boot_with_tls()
//    {
//        $caddy = new Caddy();
//        $caddy->addApp(
//            (new Http())->addServer(
//                'server1', (new Http\Server())->addRoute(
//                (new Routes())->addHandle(
//                    new StaticResponse('phpunit', 200)
//                )
//            ))
//        )->addApp((new Tls())
//            ->setAutomation((new Tls\Automation())
//                ->setOnDemand((new Tls\Automation\OnDemand())
//                    ->setAsk('/api/platform/domainCheck')
//                    ->setRateLimit((new Tls\Automation\OnDemand\RateLimit())
//                        ->setBurst('5')
//                        ->setInterval('2m')
//                    )
//                )->addPolicies((new Tls\Automation\Policies())
//                    ->addSubjects('*.localhost')
//                    ->addIssuer((new Tls\Automation\Policies\Issuers\Acme())
//                        ->setEmail('test@test.com')
//                    )
//                )
//            )
//        );
//
//        $this->assertTrue($caddy->load());
//    }

    /**
     * @covers \mattvb91\CaddyPhp\Caddy::getRemoteConfig
     */
    public function testCaddyGetConfig()
    {
        $caddy = new Caddy();
        $caddy->addApp(
            (new Http())
                ->addServer(
                    'test',
                    (new Http\Server())
                        ->addRoute(
                            (new Route())
                                ->addHandle((new StaticResponse('test')))
                        )
                )
        );
        $caddy->load();

        $this->assertEquals(json_decode(json_encode($caddy->toArray())), $caddy->getRemoteConfig());
    }
}
