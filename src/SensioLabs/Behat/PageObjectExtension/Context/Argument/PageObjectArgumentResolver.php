<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

class PageObjectArgumentResolver implements ArgumentResolver
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveArguments(\ReflectionClass $classReflection, array $arguments)
    {
        $parameters = $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : array();

        foreach ($parameters as $i => $parameter) {
            $parameterClassName = $parameter->getClass() ? $parameter->getClass()->getName() : null;

            if ($this->isPageOrElement($parameterClassName)) {
                $arguments[$i] = $this->factory->instantiate($parameterClassName);
            }
        }

        return $arguments;
    }

    /**
     * @param string|null $className
     *
     * @return boolean
     */
    private function isPageOrElement($className)
    {
        return $this->isPage($className) || $this->isElement($className);
    }

    /**
     * @param string|null $className
     *
     * @return boolean
     */
    private function isPage($className)
    {
        return null !== $className && is_subclass_of($className, 'SensioLabs\Behat\PageObjectExtension\PageObject\Page');
    }

    /**
     * @param string|null $className
     *
     * @return boolean
     */
    private function isElement($className)
    {
        return null !== $className && is_subclass_of($className, 'SensioLabs\Behat\PageObjectExtension\PageObject\Element');
    }
}
