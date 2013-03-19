<?php

namespace SensioLabs\PageObjectExtension\PageObject;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use SensioLabs\PageObjectExtension\Context\PageFactoryInterface;

abstract class PageElement extends NodeElement
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
        parent::__construct(static::xpath(), $session);

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
     * @return string
     */
    abstract static protected function xpath();

    /**
     * @param string $name
     *
     * @return PageObject|PageElement
     */
    protected function getPage($name)
    {
        return $this->pageFactory->create($name);
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return preg_replace('/^.*\\\(.*?)$/', '$1', get_called_class());
    }
}
