<?php

namespace SensioLabs\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use SensioLabs\PageObjectExtension\Context\PageFactoryInterface;
use Behat\Mink\Selector\SelectorsHandler;

abstract class Element extends NodeElement
{
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
     * @return array
     */
    abstract protected function getSelector();

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
        $selectorList = $this->getSelector();

        $selector = key($selectorList);
        $locator = $selectorList[$selector];

        return $selectorsHandler->selectorToXpath($selector, $locator);
    }
}
