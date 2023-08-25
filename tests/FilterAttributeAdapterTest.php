<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use PHPUnit\Framework\TestCase;

final class FilterAttributeAdapterTest extends TestCase
{
    public function testAdapterResultWithLikeAndContains()
    {
        $attribute = new FilterAttributeAdapter(["attr(class, a)", "like", "%vip%"]);
        $attribute->transform();

        $this->assertSame(["attr(class, a)", "like", "%vip%"], $attribute->getFilter());
        $this->assertSame("class", $attribute->getRef());
        $this->assertSame("attribute", $attribute->getRefType());
        $this->assertSame("like", $attribute->getClause());
        $this->assertSame("contains", $attribute->getClauseType());
        $this->assertSame("/^\s?vip|\svip\s|\svip$/im", $attribute->getPattern());
        $this->assertSame(" a", $attribute->getNode());
        $this->assertSame("vip", $attribute->getValue());
    }
}
