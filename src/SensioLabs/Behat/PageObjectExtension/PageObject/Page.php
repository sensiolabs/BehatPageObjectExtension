<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidPageDeclarationException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class Page extends DocumentElement
{
    /**
     * @var string|null $path
     */
    protected $path = null;

    /**
     * @var array $elements
     */
    protected $elements = array();

    /**
     * @var ParameterBag
     */
    protected $selectors;

    /**
     * @var PageFactoryInterface $pageFactory
     */
    private $pageFactory = null;

    /**
     * @var array $parameters
     */
    private $parameters = array();

    /**
     * @var SelectorFactoryInterface
     */
    private $selectorFactory;

    /**
     * @param Session                  $session
     * @param PageFactoryInterface     $pageFactory
     * @param SelectorFactoryInterface $selectorFactory
     * @param array                    $parameters
     */
    public function __construct(Session $session, PageFactoryInterface $pageFactory, SelectorFactoryInterface $selectorFactory, array $parameters = array())
    {
        parent::__construct($session);

        $this->pageFactory     = $pageFactory;
        $this->parameters      = $parameters;
        $this->selectorFactory = $selectorFactory;

        $this->selectors       = new ParameterBag();

        $this->createSelectorsFromElementsArray();
        $this->createSelectors();
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

        $this->verifyResponse();
        $this->verifyPage();

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

    protected function getParameters()
    {
        return $this->elements;
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    protected function getElement($name)
    {
        if ($this->selectors->has($name)) {
            return $this->pageFactory->createInlineElement($this->selectors->get($name));
        }

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
     * @throws UnexpectedPageException
     */
    protected function verifyResponse()
    {
        try {
            $statusCode = $this->getSession()->getStatusCode();

            if ($this->isErrorResponse($statusCode)) {
                $currentUrl = $this->getSession()->getCurrentUrl();
                $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

                throw new UnexpectedPageException($message);
            }
        } catch (DriverException $exception) {
        }
    }

    /**
     * Overload to verify if we're on an expected page. Throw an exception if not.
     */
    protected function verifyPage()
    {
    }

    /**
     * @param string $statusCode
     *
     * @return boolean
     */
    protected function isErrorResponse($statusCode)
    {
        return in_array(substr($statusCode, 0, 1), array('4', '5'));
    }

    /**
     * Create selectors in child classes with addSelector method
     *
     * @throws Exception\InvalidPageDeclarationException
     */
    protected function createSelectors()
    {
    }

    /**
     * Add new selector to selectors definition
     *
     * @param string            $name
     * @param SelectorInterface $selector
     */
    protected function addSelector($name, SelectorInterface $selector)
    {
        $this->selectors->set($name, $selector);
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

    /**
     * Converts simple array selector definition to new ParameterBag of SelectorInterfaces objects
     *
     * @throws Exception\InvalidPageDeclarationException
     */
    private function createSelectorsFromElementsArray()
    {
        foreach ($this->elements as $key => $element) {
            $this->addSelector($key, $this->selectorFactory->create($element));
        }
    }
}
