<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

class LazyFactory implements Factory
{
    /**
     * @var Factory
     */
    private $decoratedFactory;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    /**
     * @param Factory                       $decoratedFactory
     * @param LazyLoadingValueHolderFactory $proxyFactory
     */
    public function __construct(Factory $decoratedFactory, LazyLoadingValueHolderFactory $proxyFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    public function createPage($name)
    {
        return $this->decoratedFactory->createPage($name);
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    public function createElement($name)
    {
        return $this->decoratedFactory->createElement($name);
    }

    /**
     * @param string|array $selector
     * @param null|string  $name
     *
     * @return InlineElement
     */
    public function createInlineElement($selector, $name = null)
    {
        return $this->decoratedFactory->createInlineElement($selector);
    }
    /**
     * @param string $pageObjectClass
     *
     * @return LazyLoadingInterface|PageObject
     */
    public function create($pageObjectClass)
    {
        $decoratedFactory = $this->decoratedFactory;

        $initializer = function (&$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) use ($pageObjectClass, $decoratedFactory) {
            $initializer = null;

            $wrappedObject = $decoratedFactory->create($pageObjectClass);

            return true;
        };

        return $this->proxyFactory->createProxy($pageObjectClass, $initializer);
    }
}
