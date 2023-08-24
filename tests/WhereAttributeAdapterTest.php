<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Adapter\WhereAttributeAdapter;
use PHPUnit\Framework\TestCase;

final class WhereAttributeAdapterTest extends TestCase
{
    public function testAdapterResultWithLikeAndContains()
    {
        $attribute = new WhereAttributeAdapter(["attr(class, a)", "like", "%vip%"]);
        $attribute->transform();

        // $this->assertSame("attr(class, a) like '%vip%'", $attribute->getRaw());
        $this->assertSame(["attr(class, a)", "like", "%vip%"], $attribute->getWhere());
        $this->assertSame("class", $attribute->getRef());
        $this->assertSame("attribute", $attribute->getRefType());
        $this->assertSame("like", $attribute->getOperator());
        $this->assertSame("contains", $attribute->getOperatorType());
        $this->assertSame("/^\s?vip|\svip\s|\svip$/im", $attribute->getPattern());
        $this->assertSame(" a", $attribute->getNode());
        $this->assertSame("vip", $attribute->getValue());
    }
}
