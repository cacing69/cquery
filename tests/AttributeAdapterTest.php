<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use PHPUnit\Framework\TestCase;

final class AttributeAdapterTest extends TestCase
{
    public function testAdapterResultWithLikeAndContains()
    {
        $attribute = new AttributeAdapter(["attr(class, a)", "like", "%vip%"]);
        $attribute->transform();

        // $this->assertSame("attr(class, a) like '%vip%'", $attribute->getRaw());
        $this->assertSame(["attr(class, a)", "like", "%vip%"], $attribute->getRaw());
        $this->assertSame("class", $attribute->getRef());
        $this->assertSame("attribute", $attribute->getRefType());
        $this->assertSame("like", $attribute->getOperator());
        $this->assertSame("contains", $attribute->getOperatorType());
        $this->assertSame("/^\s?vip|\svip\s|\svip$/im", $attribute->getPattern());
        $this->assertSame(" a", $attribute->getSelectNode());
        $this->assertSame("vip", $attribute->getValue());
    }
}
