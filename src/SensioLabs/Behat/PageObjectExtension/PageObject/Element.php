<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;

abstract class Element extends NodeElement
{
    /**
     * @var array|string $selector
     */
    protected $selector = array('xpath' => '//');

    /**
     * @var PageFactoryInterface $pageFactory
     */
    private $pageFactory = null;

    /**
     * @param Session              $session
     * @param PageFactoryInterface $pageFactory
     */
    public function __construct(Session $session, PageFactoryInterface $pageFactory)
    {
        parent::__construct($this->getSelectorAsXpath($session->getSelectorsHandler()), $session);

        $this->pageFactory = $pageFactory;
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
     * @return Page|Element
     */
    protected function getPage($name)
    {
        return $this->pageFactory->createPage($name);
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
        $selectorType = key($this->selector);
        $locator = $this->selector[$selectorType];

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }
}
