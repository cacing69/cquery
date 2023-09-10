<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Adapter\LowerCallbackAdapter;
use Cacing69\Cquery\Adapter\UpperCallbackAdapter;
use PHPUnit\Framework\TestCase;

final class UpperCallbackAdapterTest extends TestCase
{
    public function testUpperCallbackAdapter()
    {
        $adapter = new UpperCallbackAdapter('h1');

        $_callback = $adapter->getCallback();

        $test = $_callback('test');
        $this->assertSame('TEST', $test);
    }

    public function testLowerCallbackAdapter()
    {
        $adapter = new LowerCallbackAdapter('h1');

        $_callback = $adapter->getCallback();

        $test = $_callback('TEST');
        $this->assertSame('test', $test);
    }
}
