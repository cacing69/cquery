<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Expression\ClosureCallbackExpression;
use Cacing69\Cquery\Trait\HasDefinersProperty;
use Cacing69\Cquery\Trait\HasFiltersProperty;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Closure;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

abstract class AbstractCqueryLoader
{
    use HasSourceProperty;
    use HasFiltersProperty;
    use HasDefinersProperty;
    protected $limit = null;
    protected $client;
    protected $clientName = 'browser-kit';
    protected $httpMethod = 'get';

    protected $uri = null;
    protected $isRemote = false;
    protected $isFetched = false;

    /**
     * A variable used to store the results of a query.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * The default results is null
     */
    protected $results = [];

    protected $crawler;

    protected $callbackOnContentLoaded;
    protected $callbackEachItem;
    protected $callbackOnObtainedResults;
    protected $callbackClientOnEnd;

    /**
     * Add limit amount when scraping.
     * This method is used to limit the total length of the data.
     *
     * @param int $limit set a limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Adds a source based on data given.
     * This method is used to determine the HTML element selector
     * that will serve as a property in each array element.
     *
     * @param string $value set a source element selector to activate query
     *
     * @return $this
     */
    public function from(string $value)
    {
        if ($this->source) {
            throw new CqueryException('cannot call method from twice.');
        }

        $this->filters = [];
        $this->fetchCrawler();

        $this->source = new Source($value);

        return $this;
    }

    protected function fetchCrawler()
    {
        if ($this->isRemote) {
            if ($this->clientName === 'browser-kit') {
                $this->client = new HttpBrowser(HttpClient::create());

                $this->client->request('GET', $this->uri);

                $this->crawler = new Crawler($this->client->getResponse()->getContent());

                if ($this->callbackOnContentLoaded) {
                    $_callbackOnContentLoaded = $this->callbackOnContentLoaded;

                    $this->client = $_callbackOnContentLoaded($this->client, $this->crawler);
                    $this->crawler = new Crawler($this->client->getResponse()->getContent());
                }
            } else {
                throw new CqueryException("client {$this->clientName} doesnt support");
            }
        }
    }

    /**
     * Take a first item from query result collection.
     *
     * @return array
     */
    public function first()
    {
        return $this
            ->limit(1)
            ->get()
            ->first();
    }

    /**
     * Take a last item from query result collection.
     *
     * @return array
     */
    public function last()
    {
        return $this
            ->get()
            ->last();
    }

    abstract public function get();

    public static function getResultFilter(array $filtered): array
    {
        $result = [
            'and' => [],
            'or'  => [],
        ];

        if (array_key_exists('and', $filtered) && count($filtered['and']) > 0) {
            $result['and'] = array_intersect(...$filtered['and']);
        }

        if (array_key_exists('or', $filtered) && count($filtered['or']) > 0) {
            $result['or'] = array_unique(array_merge(...$filtered['or']));
        }

        $filterResult = array_unique(array_merge($result['and'], $result['or']));

        sort($filterResult, SORT_NUMERIC);

        return $filterResult;
    }

    public function addDefiner($definer)
    {
        array_push($this->definers, new DefinerExtractor($definer, $this->source));

        $this->checkDefineNotDuplicate();

        return $this;
    }

    // TODO Add test for checking alias definer not duplicate
    protected function checkDefineNotDuplicate(): void
    {
        $_key = [];
        foreach ($this->definers as $definer) {
            if (in_array($definer->getAlias(), $_key)) {
                throw new CqueryException('the alias column must not be duplicated, only one unique name is allowed for the definer');
            }
            $_key[] = $definer->getAlias();
        }
    }

    /**
     * Adds a definer to the current source.
     *
     * This method is used to determine the HTML element selector
     * that will serve as a property in each array element.
     *
     * @param \Cacing69\Cquery\Definer|string $picks a selector to grab on element
     *
     * @throws \Cacing69\Cquery\CqueryException when the provided parameter is incorrect."
     *
     * @return $this
     */
    public function define(...$defines)
    {
        if (count($this->definers) > 0) {
            throw new CqueryException('cannot call method define twice.');
        }

        $this->validateSource();

        if ($this->isFetched) {
            $this->definers = [];
            $this->isFetched = false;
        }

        foreach ($defines as $define) {
            $this->addDefiner($define);
        }

        return $this;
    }

    protected function validateSource()
    {
        if ($this->source === null) {
            throw new CqueryException('no source defined');
        }
    }

    protected function validateDefiners()
    {
        if (count($this->definers) === 0) {
            throw new CqueryException('no definer found');
        }
    }

    public function addFilter($filter, $operator = 'and')
    {
        $this->validateSource();

        $expression = null;

        if ($filter->operatorIsCallback()) {
            $expression = new ClosureCallbackExpression(null);

            $extractor = new DefinerExtractor($filter->getNode());

            $expression = $expression
                ->setNode($extractor->getExpression()->getNode())
                ->setCallMethod($extractor->getExpression()->getCallMethod())
                ->setCallMethodParameter($extractor->getExpression()->getCallMethodParameter());
        } else {
            foreach (RegisterExpression::load() as $expression) {
                $_checkSignature = $expression::getSignature();
                if (isset($_checkSignature)) {
                    if (is_array($_checkSignature)) {
                        $_founded = false;
                        foreach ($_checkSignature as $signature) {
                            if (preg_match($signature, $filter->getNode())) {
                                $expression = new $expression($filter->getNode(), $this->source);

                                $_founded = true;
                                break;
                            }
                        }

                        if ($_founded) {
                            break;
                        }
                    } else {
                        if (preg_match($_checkSignature, $filter->getNode())) {
                            $expression = new $expression($filter->getNode(), $this->source);
                            break;
                        }
                    }
                } else {
                    $expression = new $expression($filter->getNode(), $this->source);
                }
            }
        }

        $expression->setOperator($operator);
        $expression->setFilter($filter);

        $this->filters[] = $expression;
    }

    public function setCallbackOnContentLoaded(Closure $closure)
    {
        $this->callbackOnContentLoaded = $closure;

        return $this;
    }

    public function setCallbackEachItem(Closure $closure)
    {
        $this->callbackEachItem = $closure;

        return $this;
    }

    public function setCallbackOnObtainedResults(Closure $closure)
    {
        $this->callbackOnObtainedResults = $closure;

        return $this;
    }

    public function setClientName(string $clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function setHttpMethod(string $httpMethod)
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }
}
