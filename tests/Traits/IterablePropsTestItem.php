<?php

namespace Tests\Traits;

use mattvb91\CaddyPhp\Traits\IterableProps;
use PHPUnit\Framework\TestCase;

class IterablePropsTestItem
{
    use IterableProps;

    private int $prop1;

    public function getProp1(): int
    {
        return $this->prop1;
    }

    public function setProp1(int $prop1): void
    {
        $this->prop1 = $prop1;
    }
    public string $prop2;
}
