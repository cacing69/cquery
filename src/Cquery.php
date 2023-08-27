<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Loader\HTMLLoader;
use Cacing69\Cquery\Loader\Loader;
use Cacing69\Cquery\Support\DOMManipulator;
use Tightenco\Collect\Support\Collection;

class Cquery extends Loader{
    private $loader;

    public function __construct(string $content = null, $contentType = "html", string $encoding = "UTF-8")
    {
        if($content !== null) {
            $this->loader = new HTMLLoader($content);
        }
    }

    public function pick(...$picks): Cquery
    {
        $this->loader->pick(...$picks);
        return $this;
    }

    public function from(string $value)
    {
        $this->loader->from($value);
        return $this;
    }

    public function limit(int $limit)
    {
        $this->loader->limit($limit);
        return $this;
    }

    public function first()
    {
        return $this->loader->first();
    }

    public function filter(...$filter): Cquery
    {
        $this->loader->filter(...$filter);
        return $this;
    }

    public function OrFilter(...$filter) : Cquery
    {
        $this->loader->OrFilter(...$filter);
        return $this;
    }
    public function get() : Collection
    {
        return $this->loader->get();
    }

    protected function validateSource()
    {
        $this->loader->validateSource();
    }

    public function getActiveSource(): DOMManipulator
    {
        if(get_class($this->loader) === HTMLLoader::class) {
            return $this->loader->getActiveDom();
        }

        return null;
    }

    public function setContent(string $args)
    {
        $this->loader->setContent($args);
        return $this;
    }
}
