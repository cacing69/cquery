<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Definer;
use Closure;
use PHPUnit\Framework\TestCase;

final class DefinerTest extends TestCase
{
    public function testDefinerString()
    {
        $definer = new Definer('a');

        $this->assertSame('a', $definer->getNode());
        $this->assertSame('a', $definer->getAlias());
        $this->assertTrue(is_string($definer->getRaw()));
    }

    public function testDefinerStringWithAlias()
    {
        $definer = new Definer('h1', 'title');

        $this->assertSame('h1', $definer->getNode());
        $this->assertSame('title', $definer->getAlias());
        $this->assertTrue(is_string($definer->getRaw()));
    }

    public function testDefinerStringWithoutAlias()
    {
        $definer = new Definer('(h1 > p > ul)');

        $this->assertSame('h1 > p > ul', $definer->getNode());
        $this->assertSame('h1_p_ul', $definer->getAlias());
        $this->assertTrue(is_string($definer->getRaw()));
    }

    public function testDefinerStringWithoutAliasArgument()
    {
        $definer = new Definer('(h1 > p > ul)', '_header_1');

        $this->assertSame('h1 > p > ul', $definer->getNode());
        $this->assertSame('_header_1', $definer->getAlias());
        $this->assertTrue(is_string($definer->getRaw()));
    }

    public function testDefinerWithCallback()
    {
        $definer = new Definer('(h1 > p > ul)', '_header_closure_1', function ($e) {return $e.'-TEST'; });

        $this->assertSame('h1 > p > ul', $definer->getNode());
        $this->assertSame('_header_closure_1', $definer->getAlias());
        $this->assertSame(Closure::class, get_class($definer->getRaw()));
    }

    public function testDefinerNestedWithAlias()
    {
        $definer = new Definer('length(attr(class, a))');

        $this->assertSame('length(attr(class, a))', $definer->getNode());
        $this->assertSame('length_attr_class_a', $definer->getAlias());
    }
}
