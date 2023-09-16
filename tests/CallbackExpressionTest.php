<?php

use Cacing69\Cquery\Expression\LowerCallbackExpression;
use Cacing69\Cquery\Expression\UpperCallbackExpression;
use PHPUnit\Framework\TestCase;

final class CallbackExpressionTest extends TestCase
{
    public function testUpperCallbackExpression()
    {
        $expression = new UpperCallbackExpression('h1');

        $_callback = $expression->getCallback();

        $test = $_callback('test');
        $this->assertSame('TEST', $test);
    }

    public function testLowerCallbackExpression()
    {
        $expression = new LowerCallbackExpression('h1');

        $_callback = $expression->getCallback();

        $test = $_callback('TEST');
        $this->assertSame('test', $test);
    }
}
