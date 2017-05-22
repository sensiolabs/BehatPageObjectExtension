<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Mink;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\PageObject;

class DefaultFactory implements Factory
{
    /**
     * @var Mink
     */
    private $mink = null;

    /**
     * @var ClassNameResolver
     */
    private $classNameResolver;

    /**
     * @var array
     */
    private $pageParameters = array();

    /**
     * @param Mink              $mink
     * @param ClassNameResolver $classNameResolver
     * @param array             $pageParameters
     */
    public function __construct(Mink $mink, ClassNameResolver $classNameResolver, array $pageParameters)
    {
        $this->mink = $mink;
        $this->pageParameters = $pageParameters;
        $this->classNameResolver = $classNameResolver;
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    public function createPage($name)
    {
        $pageClass = $this->classNameResolver->resolvePage($name);

        return $this->instantiatePage($pageClass);
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    public function createElement($name)
    {
        $elementClass = $this->classNameResolver->resolveElement($name);

        return $this->instantiateElement($elementClass);
    }

    /**
     * @param array|string $selector
     * @param null|string  $name
     *
     * @return InlineElement
     */
    public function createInlineElement($selector, $name = null)
    {
        $elementClass = $this->classNameResolver->resolveElement($name ? $name : 'SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement');

        if ('SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement' !== $elementClass && !is_subclass_of($elementClass, 'SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement')) {
            throw new \InvalidArgumentException(sprintf('Not a page object class: %s', ''));
        }

        return new $elementClass($selector, $this->mink->getSession(), $this);
    }

    /**
     * @param string $pageObjectClass
     *
     * @return PageObject
     */
    public function create($pageObjectClass)
    {
        if (is_subclass_of($pageObjectClass, 'SensioLabs\Behat\PageObjectExtension\PageObject\Page')) {
            return $this->instantiatePage($pageObjectClass);
        } elseif (is_subclass_of($pageObjectClass, 'SensioLabs\Behat\PageObjectExtension\PageObject\Element')) {
            return $this->instantiateElement($pageObjectClass);
        }

        throw new \InvalidArgumentException(sprintf('Not a page object class: %s', $pageObjectClass));
    }

    /**
     * @param string $pageClass
     *
     * @return Page
     */
    private function instantiatePage($pageClass)
    {
        return new $pageClass($this->mink->getSession(), $this, $this->pageParameters);
    }

    /**
     * @param string $elementClass
     *
     * @return Element
     */
    private function instantiateElement($elementClass)
    {
        return new $elementClass($this->mink->getSession(), $this);
    }
}
