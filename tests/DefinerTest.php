<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Definer;
use PHPUnit\Framework\TestCase;

final class DefinerTest extends TestCase
{
    public function testDefinerString()
    {
        $definer = new Definer("a");

        $this->assertSame("a", $definer->getNode());
        $this->assertSame("a", $definer->getAlias());
    }

    public function testDefinerStringWithAlias()
    {
        $definer = new Definer("h1", "title");

        $this->assertSame("h1", $definer->getNode());
        $this->assertSame("title", $definer->getAlias());
        $this->assertCount(1, $definer->getOptions());
    }

    public function testDefinerStringWithoutAlias()
    {
        $definer = new Definer("(h1 > p > ul)");

        $this->assertSame("h1 > p > ul", $definer->getNode());
        $this->assertSame("h1_p_ul", $definer->getAlias());
        $this->assertCount(0, $definer->getOptions());
    }
}
