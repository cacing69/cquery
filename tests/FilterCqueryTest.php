<?php

use Cacing69\Cquery\AbstractCqueryLoader;
use PHPUnit\Framework\TestCase;

final class FilterCqueryTest extends TestCase
{
    public function testFilterAnd()
    {
        $filter = [
            'and' => [
                [0, 1, 2, 3],
                [3, 2, 4],
            ],
            'or' => [],
        ];

        $resultFilter = AbstractCqueryLoader::getResultFilter($filter);

        $this->assertIsArray($resultFilter);
        $this->assertSame([2, 3], $resultFilter);
    }

    public function testFilterOr()
    {
        $filter = [
            'and' => [],
            'or'  => [
                [0, 1, 2, 3],
                [3, 2, 4],
            ],
        ];

        $resultFilter = AbstractCqueryLoader::getResultFilter($filter);

        $this->assertIsArray($resultFilter);
        $this->assertSame([0, 1, 2, 3, 4], $resultFilter);
    }

    public function testFilterOrAnd()
    {
        $filter = [
            'and' => [
                [3, 5, 7],
                [7, 9, 10],
            ],
            'or' => [
                [3, 5],
                [3, 2, 4],
            ],
        ];

        $resultFilter = AbstractCqueryLoader::getResultFilter($filter);

        $this->assertIsArray($resultFilter);
        $this->assertSame([2, 3, 4, 5, 7], $resultFilter);
    }
}
