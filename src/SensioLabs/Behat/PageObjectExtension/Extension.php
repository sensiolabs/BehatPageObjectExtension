<?php

namespace SensioLabs\Behat\PageObjectExtension;

use Behat\Behat\Extension\ExtensionInterface;
use SensioLabs\Behat\PageObjectExtension\Compiler\NamespacesPass;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Extension implements ExtensionInterface
{
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');

        $this->updateNamespaceParameters($config, $container);
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('namespaces')
                    ->children()
                        ->scalarNode('page')->end()
                        ->scalarNode('element')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @return array
     */
    public function getCompilerPasses()
    {
        return array(
            new NamespacesPass()
        );
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function updateNamespaceParameters(array $config, ContainerBuilder $container)
    {
        if (!isset($config['namespaces'])) {
            return;
        }

        if (isset($config['namespaces']['page'])) {
            $container->setParameter(
                'sensio_labs.page_object_extension.namespaces.page',
                $config['namespaces']['page']
            );
        }

        if (isset($config['namespaces']['element'])) {
            $container->setParameter(
                'sensio_labs.page_object_extension.namespaces.element',
                $config['namespaces']['element']
            );
        }
    }
}
