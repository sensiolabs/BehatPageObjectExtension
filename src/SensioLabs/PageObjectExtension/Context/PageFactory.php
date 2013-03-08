<?php

namespace SensioLabs\PageObjectExtension\Context;

use Behat\Mink\Session;
use SensioLabs\PageObjectExtension\PageObject\PageObject;

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
     * @var Session $session
     * @var array   $parameters
     */
    public function __construct(Session $session, array $parameters)
    {
        $this->session = $session;
        $this->parameters = $parameters;
    }

    /**
     * @param string $page
     *
     * @return PageObject
     */
    public function create($page)
    {
        $pageObjectClass = $this->getPageObjectClass($page);

        return new $pageObjectClass($this->session, $this->parameters);
    }

    /**
     * @param string $page
     *
     * @return string
     */
    protected function getPageObjectClass($page)
    {
        return '\\'.str_replace(' ', '', ucwords($page));
    }
}
