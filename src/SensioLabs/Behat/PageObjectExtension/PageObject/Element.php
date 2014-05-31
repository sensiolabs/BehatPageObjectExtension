<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;

abstract class Element extends NodeElement implements PageObject
{
    /**
     * @var array|string
     */
    protected $selector = array('xpath' => '//');

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
     * @return Page
     */
    protected function getPage($name)
    {
        return $this->factory->createPage($name);
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
