<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;

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
        $url = $this->getUrl($urlParameters);

        $this->getSession()->visit($url);

        $this->verify($urlParameters);

        return $this;
    }

    /**
     * @param array $urlParameters
     *
     * @return boolean
     */
    public function isOpen(array $urlParameters = array())
    {
        try {
            $this->verify($urlParameters);
        } catch (\Exception $e) {
            return false;
        }

        return true;
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
     * @return Element
     */
    public function getElement($name)
    {
        $element = $this->createElement($name);

        if (!$this->has('xpath', $element->getXpath())) {
            throw new ElementNotFoundException(sprintf('"%s" element is not present on the page', $name));
        }

        return $element;
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
     * @return boolean
     */
    protected function hasElement($name)
    {
        return $this->has('xpath', $this->createElement($name)->getXpath());
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    protected function createElement($name)
    {
        if (isset($this->elements[$name])) {
            return $this->pageFactory->createInlineElement($this->elements[$name]);
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
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = array())
    {
        return $this->makeSurePathIsAbsolute($this->unmaskUrl($urlParameters));
    }

    /**
     * @param array $urlParameters
     */
    protected function verify(array $urlParameters)
    {
        $this->verifyResponse();
        $this->verifyUrl($urlParameters);
        $this->verifyPage();
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
     * Overload to verify if the current url matches the expected one. Throw an exception otherwise.
     *
     * @param array $urlParameters
     */
    protected function verifyUrl(array $urlParameters = array())
    {
    }

    /**
     * Overload to verify if we're on an expected page. Throw an exception otherwise.
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
