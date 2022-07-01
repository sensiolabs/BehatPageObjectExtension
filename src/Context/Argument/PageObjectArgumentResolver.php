<?php

namespace SensioLabs\Behat\PageObjectExtension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

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
        $parameters = $this->getConstructorParameters($classReflection);

        foreach ($parameters as $i => $parameter) {
            $parameterClassName = $this->getClassName($parameter);

            if (null !== $parameterClassName && $this->isPageOrElement($parameterClassName)) {
                $arguments[$i] = $this->factory->create($parameterClassName);
            }
        }

        return $arguments;
    }

    /**
     * @param string $className
     *
     * @return boolean
     */
    private function isPageOrElement($className)
    {
        return $this->isPage($className) || $this->isElement($className);
    }

    /**
     * @param string $className
     *
     * @return boolean
     */
    private function isPage($className)
    {
        return is_subclass_of($className, Page::class);
    }

    /**
     * @param string $className
     *
     * @return boolean
     */
    private function isElement($className)
    {
        return is_subclass_of($className, Element::class);
    }

    /**
     * @param \ReflectionClass $classReflection
     *
     * @return \ReflectionParameter[]
     */
    private function getConstructorParameters(\ReflectionClass $classReflection)
    {
        return $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : array();
    }

    /**
     * @param \ReflectionParameter $parameter
     *
     * @return string|null
     */
    private function getClassName(\ReflectionParameter $parameter)
    {
        if (\PHP_VERSION_ID < 80000) {
            return $parameter->getClass() ? $parameter->getClass()->getName() : null;
        }

        return $parameter->getType() ? $parameter->getType()->getName() : null;
    }
}
