<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Behat\Mink\Mink;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

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
     * @var Mink  $mink
     * @var array $pageParameters
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

        return new $pageClass($this->mink->getSession(), $this, $this->pageParameters);
    }

    /**
     * @param string $name
     *
     * @return Element
     */
    public function createElement($name)
    {
        $elementClass = $this->classNameResolver->resolveElement($name);

        return new $elementClass($this->mink->getSession(), $this);
    }

    /**
     * @param array|string $selector
     *
     * @return InlineElement
     */
    public function createInlineElement($selector)
    {
        return new InlineElement($selector, $this->mink->getSession(), $this);
    }
}
