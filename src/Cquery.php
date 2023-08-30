<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Loader\HTMLLoader;
use Cacing69\Cquery\DOMManipulator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;


/**
 * Cquery eases query of a list of data given.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 */
class Cquery {
    /**
     * loader should be an instance of Cacing69\Loader\Loader
     * Available loader HTMLLoader, JSONLoader(), CSVLoader
     *
     * @var \Cacing69\Cquery\Loader
     *
     * The default loader is null, u need to specify when create Cquery instance.
     */
    private $loader;

    /**
     * @param \DOMNodeList|\DOMNode|string|null $source A source to use as the the source data, u can put html content/url page to scrape default is null
     * @param string $contentType Type of Data Content to be Used as Data Source default is 'html'
     * @param string $encoding Encoding Used in the Content default is 'UTF-8'
     */
    public function __construct(string $source = null, $contentType = "html", string $encoding = "UTF-8")
    {
        if($source !== null) {
            if (filter_var($source, FILTER_VALIDATE_URL)) {
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, $source);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // $output = curl_exec($ch);
                // $this->loader = new HTMLLoader($output);
                // curl_close($ch);

                $browser = new HttpBrowser(HttpClient::create());
                $browser->request('GET', $source);

                $response = $browser->getResponse()->getContent();

                $this->loader = new HTMLLoader($response);
            } else {
                if($contentType === "html") {
                    $this->loader = new HTMLLoader($source);
                }
            }
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
    public function get() : ArrayCollection
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
