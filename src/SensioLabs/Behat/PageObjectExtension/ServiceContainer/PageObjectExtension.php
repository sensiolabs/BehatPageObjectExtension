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

        $this->updateNamespaceParameters($container, isset($config['namespaces']) ? $config['namespaces'] : array());
        $this->updatePageObjectFactoryDefinition($container, isset($config['factory']) ? $config['factory'] : array());
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
                ->arrayNode('page_parameters')
                    ->info('parameters passed from the factory when creating a page')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('proxies_target_dir')
                    ->info('Target directory for proxies generated for the lazy factory')
                    ->defaultValue(sys_get_temp_dir())
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

        if (!empty($factory['page_parameters'])) {
            $container->setParameter('sensio_labs.page_object_extension.page_factory.page_parameters', $factory['page_parameters']);
        }

        $this->configureFactoryProxies($container, $factory);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $factory
     */
    private function configureFactoryProxies(ContainerBuilder $container, array $factory)
    {
        $proxiesTargetDir = !empty($factory['proxies_target_dir']) ? $factory['proxies_target_dir'] : sys_get_temp_dir();
        $container->setParameter('sensio_labs.page_object_extension.proxies_target_dir', $proxiesTargetDir);

        if (!interface_exists('ProxyManager\Proxy\LazyLoadingInterface')) {
            $container->removeDefinition('sensio_labs.page_object_extension.context.argument_resolver.page_object');
        }
    }
}
