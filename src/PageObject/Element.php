<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;

abstract class Element extends NodeElement implements PageObject
{
    /**
     * @var array|string
     */
    protected $selector = array('xpath' => '//');

    /**
     * @var array
     */
    protected $elements = array();

    /**
     * @var Factory
     */
    private $factory = null;

    /**
     * @param Session $session
     * @param Factory $factory
     */
    public function __construct(Session $session, Factory $factory)
    {
        parent::__construct($this->getSelectorAsXpath($session->getSelectorsHandler()), $session);

        $this->factory = $factory;
    }

    /**
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, $arguments)
    {
        $message = sprintf('"%s" method is not available on the %s', $name, $this->getName());

        throw new \BadMethodCallException($message);
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
        return $this->factory->createPage($name);
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
            return $this->factory->createInlineElement($this->elements[$name]);
        }

        return $this->factory->createElement($name);
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return preg_replace('/^.*\\\(.*?)$/', '$1', get_called_class());
    }

    /**
     * @param SelectorsHandler $selectorsHandler
     *
     * @return string
     */
    private function getSelectorAsXpath(SelectorsHandler $selectorsHandler)
    {
        $selectorType = is_array($this->selector) ? key($this->selector) : 'css';
        $locator = is_array($this->selector) ? $this->selector[$selectorType] : $this->selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }
}
