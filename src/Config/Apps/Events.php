<?php

declare(strict_types=1);

namespace mattvb91\CaddyPhp\Config\Apps;

use mattvb91\CaddyPhp\Config\Apps\Events\Subscription;
use mattvb91\CaddyPhp\Interfaces\App;

class Events implements App
{
    /** @var Subscription[] */
    private array $subscriptions;

    /**
     * @param Subscription[] $subscriptions
     */
    public function __construct(array $subscriptions = [])
    {
        $this->subscriptions = $subscriptions;
    }

    public function toArray(): array
    {
        return [
            'subscriptions' => [
                ...array_map(function (Subscription $subscription): array {
                    return $subscription->toArray();
                }, $this->subscriptions),
            ],
        ];
    }
}
