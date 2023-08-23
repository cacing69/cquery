<?php
namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use Cacing69\Cquery\Extractor\WhereExtractor;
use Cacing69\Cquery\Extractor\SelectorExtractor;
use Cacing69\Cquery\Support\HasSelectorProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Cquery {
    use HasSelectorProperty;

    private $crawler;
    private $converter;
    private $column = [];
    private $where = [];
    private $results = [];

    public function __construct($content = null)
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->crawler = new Crawler($content);
        }

        return $this;
    }

    public function select(...$selects)
    {
        foreach ($selects as $select) {
            $decodeSelect = explode(" as ", $select);
            $this->column[] = [
                "selector" => trim($decodeSelect[0]),
                "key" => trim($decodeSelect[1]),
            ];
        }

        return $this;
    }

    public function from($value)
    {
        $this->selector = new SelectorExtractor($value);
        return $this;
    }

    public function first()
    {
        return $this->get()->first();
    }

    public function where(...$where)
    {
        $buildWhere = new WhereExtractor($where);

        $this->where[] = $buildWhere
                        ->setSelector($this->selector)
                        ->extract();
        return $this;
    }

    public function get()
    {
        // WHERE CHECKING DISINI
        if(count($this->where) > 0) {
            $_keep = [];

            foreach ($this->where as $key => $value) {
                $cssToXpathWhere = $this->converter->toXPath($this->selector->getValue() . $value->getSelectNode());

                $this->crawler->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_keep, $value) {
                    if ($value instanceof AttributeAdapter) {
                        if (preg_match($value->getPattern(), $node->attr($value->getRef()))) { // regex khusus like %vip%
                            array_push($_keep, $i);
                        }
                    }
                });
            }

            $parentXPath = $this->converter->toXPath($this->selector);

            $this->crawler->filterXPath($parentXPath)->each(function (Crawler $crawler, $i) use (&$_keep) {
                if (!in_array($i, $_keep)) {
                    $node = $crawler->getNode(0);
                    $node->parentNode->removeChild($node);
                }
            });
        }

        // PROSES DOM DISINI
        foreach ($this->column as $column) {

            if(preg_match("/^attr(.*, .*)$/is", $column["selector"])){
                preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $column["selector"], $attr);
                preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $column["selector"], $pick);

                $cssToXpath = $this->converter->toXPath($this->selector . " " . $pick[1]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $attr) {
                    $this->results[$i][$column["key"]] = $node->attr($attr[1]);
                });
            } else {
                $cssToXpath = $this->converter->toXPath($this->selector." ". $column["selector"]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column){
                    $this->results[$i][$column["key"]] = $node->innerText(false);
                });
            }
        }

        return collect($this->results);
    }
}
