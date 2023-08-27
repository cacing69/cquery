<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Picker;
use PHPUnit\Framework\TestCase;

final class PickerTest extends TestCase
{
    public function testPickerString()
    {
        $picker = new Picker("a");

        $this->assertSame("a", $picker->getNode());
        $this->assertSame("a", $picker->getAlias());
        $this->assertCount(0, $picker->getOptions());
    }

    public function testPickerStringWithAlias()
    {
        $picker = new Picker("h1", "title");

        $this->assertSame("h1", $picker->getNode());
        $this->assertSame("title", $picker->getAlias());
        $this->assertCount(1, $picker->getOptions());
    }

    public function testPickerStringWithoutAlias()
    {
        $picker = new Picker("(h1 > p > ul)");

        $this->assertSame("h1 > p > ul", $picker->getNode());
        $this->assertSame("h1_p_ul", $picker->getAlias());
        $this->assertCount(0, $picker->getOptions());
    }
}
