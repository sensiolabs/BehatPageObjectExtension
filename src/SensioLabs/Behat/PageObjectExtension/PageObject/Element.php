<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactoryInterface;

abstract class Element extends NodeElement
{
    /**
     * @var PageFactoryInterface $pageFactory
     */
    private $pageFactory = null;

    /**
     * @var SelectorFactoryInterface
     */
    private $selectorFactory;

    /**
     * @var array
     */
    protected $selector = array('xpath' => '//');

    /**
     * @param Session                  $session
     * @param PageFactoryInterface     $pageFactory
     * @param SelectorFactoryInterface $selectorFactory
     */
    public function __construct(Session $session, PageFactoryInterface $pageFactory, SelectorFactoryInterface $selectorFactory)
    {
        $this->selectorFactory = $selectorFactory;
        $this->pageFactory = $pageFactory;

        parent::__construct($this->getSelectorAsXpath($session->getSelectorsHandler()), $session);
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
     * Return scope limit selector
     */
    protected function getSelector()
    {
        return $this->selectorFactory->create($this->selector);
    }

    /**
     * @param SelectorsHandler $selectorsHandler
     *
     * @return string
     */
    private function getSelectorAsXpath(SelectorsHandler $selectorsHandler)
    {
        $selector = $this->getSelector();
        if ($selector == null) {
            return null;
        }

        return $selectorsHandler->selectorToXpath($selector->getType(), $selector->getPath());
    }
}
