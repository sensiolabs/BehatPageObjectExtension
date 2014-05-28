<?php

namespace SensioLabs\Behat\PageObjectExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class PageObjectExtension implements TestworkExtension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'page_object';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
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
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('services.xml');

        $this->updateNamespaceParameters($container, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function updateNamespaceParameters(ContainerBuilder $container, array $config)
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
