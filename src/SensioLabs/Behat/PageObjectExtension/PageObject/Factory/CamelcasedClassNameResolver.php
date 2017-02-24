<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

class CamelcasedClassNameResolver implements ClassNameResolver
{
    /**
     * @var array
     */
    private $pageNamespaces = array();

    /**
     * @var array
     */
    private $elementNamespaces = array();

    /**
     * @param array $pageNamespaces
     * @param array $elementNamespaces
     */
    public function __construct(array $pageNamespaces = array('\\'), array $elementNamespaces = array('\\'))
    {
        $this->addPageNamespaces($pageNamespaces);
        $this->addElementNamespaces($elementNamespaces);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolvePage($name)
    {
        return $this->resolve($name, $this->pageNamespaces, 'page');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolveElement($name)
    {
        return $this->resolve($name, $this->elementNamespaces, 'element');
    }

    /**
     * @param string $pageObjectName
     * @param array  $namespaces
     * @param string $pageObjectType
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function resolve($pageObjectName, array $namespaces, $pageObjectType)
    {
        if ($this->isPageObjectNameFQCN($pageObjectName)) {
            return $pageObjectName;
        }

        $classNameCandidates = $this->getClassNameCandidates($namespaces, $pageObjectName);
        $className = $this->findExistingClassName($classNameCandidates);

        if (null !== $className) {
            return $className;
        }

        $message = sprintf('Could not find a class for the "%s" %s. ', $pageObjectName, $pageObjectType);
        $message.= sprintf('None of the configured namespaces worked: "%s"', implode($classNameCandidates, ', '));

        throw new \InvalidArgumentException($message);
    }

    /**
     * @param array  $namespaces
     * @param string $name
     *
     * @return array
     */
    private function getClassNameCandidates(array $namespaces, $name)
    {
        $className = str_replace(' ', '', ucwords($name));

        return array_map(function ($namespace) use ($className) { return $namespace.$className; }, $namespaces);
    }

    /**
     * @param array $classCandidates
     *
     * @return null|string
     */
    private function findExistingClassName(array $classCandidates)
    {
        foreach ($classCandidates as $candidate) {
            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param string $pageObjectName
     *
     * @return bool
     */
    private function isPageObjectNameFQCN($pageObjectName)
    {
        return class_exists($pageObjectName);
    }

    /**
     * @param array $pageNamespaces
     */
    private function addPageNamespaces(array $pageNamespaces)
    {
        foreach ($pageNamespaces as $namespace) {
            $this->pageNamespaces[] = $this->normalizeNamespace($namespace);
        }
    }

    /**
     * @param array $elementNamespaces
     */
    private function addElementNamespaces(array $elementNamespaces)
    {
        foreach ($elementNamespaces as $namespace) {
            $this->elementNamespaces[] = $this->normalizeNamespace($namespace);
        }
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    private function normalizeNamespace($namespace)
    {
        $namespace = rtrim($namespace, '\\').'\\';

        if (0 !== strpos($namespace, '\\')) {
            $namespace = '\\'.$namespace;
        }

        return $namespace;
    }
}
