<?php

namespace SensioLabs\Behat\PageObjectExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class NamespacesPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $pageNamespace = $this->makeSurePageNamespaceIsSet($container);
        $this->makeSureElementNamespaceIsSet($container, $pageNamespace);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string
     */
    private function makeSurePageNamespaceIsSet(ContainerBuilder $container)
    {
        $pageNamespace = $container->getParameter('sensio_labs.page_object_extension.namespaces.page');

        if (null === $pageNamespace) {
            $pageNamespace = $this->guessPageNamespace($container);
            $container->setParameter('sensio_labs.page_object_extension.namespaces.page', $pageNamespace);
        }

        return $pageNamespace;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $pageNamespace
     *
     * @return string
     */
    private function makeSureElementNamespaceIsSet(ContainerBuilder $container, $pageNamespace)
    {
        $elementNamespace = $container->getParameter('sensio_labs.page_object_extension.namespaces.element');

        if (null === $elementNamespace) {
            $elementNamespace = $this->guessElementNamespace($pageNamespace);
            $container->setParameter('sensio_labs.page_object_extension.namespaces.element', $elementNamespace);
        }

        return $elementNamespace;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string
     */
    private function guessPageNamespace(ContainerBuilder $container)
    {
        $contextClass = (string) $container->getParameter('behat.context.class');
        $contextNamespace = preg_replace('/^(.+)\\\\[^\\\\]+$/', '$1', $contextClass);

        if ($contextNamespace !== $contextClass) {
            return $contextNamespace.'\\Page';
        }

        return '\\';
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string
     */
    private function guessElementNamespace($pageNamespace)
    {
        return $pageNamespace === '\\' ? '\\' : $pageNamespace.'\\Element';
    }
}
