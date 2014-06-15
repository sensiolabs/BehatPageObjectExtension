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
        $this->configureNamespaces($builder);
        $this->configurePageObjectFactory($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/config'));
        $loader->load('services.xml');

        if (isset($config['namespaces'])) {
            $this->updateNamespaceParameters($container, $config['namespaces']);
        }

        if (isset($config['factory'])) {
            $this->updatePageObjectFactoryDefinition($container, $config['factory']);
        }

        if (!interface_exists('ProxyManager\Proxy\LazyLoadingInterface')) {
            $container->removeDefinition('sensio_labs.page_object_extension.context.argument_resolver.page_object');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    private function configureNamespaces(ArrayNodeDefinition $builder)
    {
        $namespaces = $builder->children()->arrayNode('namespaces')->children();

        foreach (array('page', 'element') as $namespaceType) {
            $namespace = $namespaces->arrayNode($namespaceType);
            $namespace->beforeNormalization()->ifString()->then(function ($v) { return array($v); } );
            $namespace->prototype('scalar');
        }
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    private function configurePageObjectFactory(ArrayNodeDefinition $builder)
    {
        $factory = $builder->children()->arrayNode('factory');
        $factory
            ->beforeNormalization()
                ->ifString()
                ->then(function ($v) { return array('id' => $v); })
            ->end()
            ->children()
                ->scalarNode('id')
                    ->info('id of a page object factory service')
                ->end()
                ->scalarNode('class_name_resolver')
                    ->info('id of a class name resolver service for the default factory')
                ->end()
            ->end();
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $namespaces
     */
    private function updateNamespaceParameters(ContainerBuilder $container, array $namespaces)
    {
        if (!empty($namespaces['page'])) {
            $container->setParameter('sensio_labs.page_object_extension.namespaces.page', $namespaces['page']);
            $namespaces['element'] = $namespaces['element'] ?: array_map(function ($v) { return $v.'\Element'; }, $namespaces['page']);
        }

        if (!empty($namespaces['element'])) {
            $container->setParameter('sensio_labs.page_object_extension.namespaces.element', $namespaces['element']);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $factory
     */
    private function updatePageObjectFactoryDefinition(ContainerBuilder $container, array $factory)
    {
        if (isset($factory['id'])) {
            $container->setAlias('sensio_labs.page_object_extension.page_factory', $factory['id']);
        }

        if (isset($factory['class_name_resolver'])) {
            $container->setAlias('sensio_labs.page_object_extension.class_name_resolver', $factory['class_name_resolver']);
        }
    }
}
