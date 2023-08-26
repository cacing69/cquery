<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Filter\FilterAttributeAdapter;
use PHPUnit\Framework\TestCase;

final class FilterAttributeAdapterTest extends TestCase
{
    public function testAdapterResultWithHasAndContains()
    {
        $attribute = new FilterAttributeAdapter(["attr(class, a)", "has", "vip"]);
        $attribute->transform();

        $this->assertSame(["attr(class, a)", "has", "vip"], $attribute->getFilter());
        $this->assertSame("class", $attribute->getRef());
        $this->assertSame("attribute", $attribute->getRefType());
        $this->assertSame("has", $attribute->getClause());
        $this->assertSame("has", $attribute->getClauseType());
        $this->assertSame("/^\s?vip|\svip\s|\svip$/im", $attribute->getPattern());
        $this->assertSame(" a", $attribute->getNode());
        $this->assertSame("vip", $attribute->getValue());
    }
}
