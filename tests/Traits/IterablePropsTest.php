<?php

namespace Tests\Traits;

use mattvb91\CaddyPhp\Traits\IterableProps;
use PHPUnit\Framework\TestCase;

class IterablePropsTest extends TestCase
{
    /**
     * @covers \mattvb91\CaddyPhp\Traits\IterableProps::iterateAllProperties
     */
    public function testIterateAllProperties(): void
    {
        $testItem = new IterablePropsTestItem();
        $testItem->setProp1(42);
        $testItem->prop2 = 'value2';
        $this->assertEquals(42, $testItem->getProp1());
        $this->assertEquals([
            'prop1' => 42,
            'prop2' => 'value2'
        ], $testItem->iterateAllProperties());
    }
}
