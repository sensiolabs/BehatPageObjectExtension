<?php

namespace SensioLabs\PageObjectExtension\PageObject;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Session;
use SensioLabs\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\PageObjectExtension\PageObject\Exception\PathNotProvidedException;

abstract class Page extends DocumentElement
{
    /**
     * @var string|null
     */
    protected $path = null;

    /**
     * @var PageFactoryInterface $pageFactory
     */
    private $pageFactory = null;

    /**
     * @var array $parameters
     */
    private $parameters = array();

    /**
     * @param Session              $session
     * @param PageFactoryInterface $pageFactory
     * @param array                $parameters
     */
    public function __construct(Session $session, PageFactoryInterface $pageFactory, array $parameters = array())
    {
        parent::__construct($session);

        $this->pageFactory = $pageFactory;
        $this->parameters = $parameters;
    }

    /**
     * @param array $urlParameters
     *
     * @return Page
     */
    public function open(array $urlParameters = array())
    {
        $path = $this->unmaskUrl($urlParameters);
        $path = $this->makeSurePathIsAbsolute($path);

        $this->getSession()->visit($path);

        return $this;
    }

    /**
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, $arguments)
    {
        throw new \BadMethodCallException(sprintf('"%s" method is not available on the %s', $name, $this->getName()));
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    protected function getPage($name)
    {
        return $this->pageFactory->createPage($name);
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    protected function getElement($name)
    {
        return $this->pageFactory->createElement($name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return preg_replace('/^.*\\\(.*?)$/', '$1', get_called_class());
    }

    /**
     * @throws PathNotProvidedException
     *
     * @return string
     */
    protected function getPath()
    {
        if (null === $this->path) {
            throw new PathNotProvidedException('You must add a path property to your page object');
        }

        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function makeSurePathIsAbsolute($path)
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/').'/';

        return 0 !== strpos($path, 'http') ? $baseUrl.ltrim($path, '/') : $path;
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    private function unmaskUrl(array $urlParameters)
    {
        $url = $this->getPath();

        foreach ($urlParameters as $parameter => $value) {
            $url = str_replace(sprintf('{%s}', $parameter), $value, $url);
        }

        return $url;
    }
}
