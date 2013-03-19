<?php

namespace SensioLabs\PageObjectExtension\Context;

use Behat\Mink\Session;
use SensioLabs\PageObjectExtension\PageObject\Element;
use SensioLabs\PageObjectExtension\PageObject\Page;

class PageFactory implements PageFactoryInterface
{
    /**
     * @var Session $session
     */
    private $sesion = null;

    /**
     * @var array $parameters
     */
    private $parameters = array();

    /**
     * @var string $namespace
     */
    private $namespace = '\\';

    /**
     * @var Session $session
     * @var array   $parameters
     */
    public function __construct(Session $session, array $parameters)
    {
        $this->session = $session;
        $this->parameters = $parameters;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = rtrim($namespace, '\\').'\\';
    }

    /**
     * @param string $page
     *
     * @return Page|Element
     */
    public function create($page)
    {
        $pageObjectClass = $this->getPageObjectClass($page);

        if (!class_exists($pageObjectClass)) {
            throw new \LogicException(sprintf('"%s" page not recognised', $page));
        }

        return new $pageObjectClass($this->session, $this, $this->parameters);
    }

    /**
     * @param string $page
     *
     * @return string
     */
    protected function getPageObjectClass($page)
    {
        if ($this->namespace === '\\') {
            $page = preg_replace('/^.*\/\s+(.*?)$/', '$1', $page);
        }

        $page = str_replace(array(' ', '/'), array('', '\\'), ucwords($page));

        return $this->namespace.$page;
    }
}
