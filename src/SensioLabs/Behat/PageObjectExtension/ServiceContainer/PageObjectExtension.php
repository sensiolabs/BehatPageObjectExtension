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
        $namespaces = $builder->children()->arrayNode('namespaces')->children();

        foreach (array('page', 'element') as $namespaceType) {
            $namespace = $namespaces->arrayNode($namespaceType);
            $namespace->beforeNormalization()->ifString()->then(function ($v) { return array($v); } );
            $namespace->prototype('scalar');
        }

        $builder->children()->scalarNode('factory');
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
            $container->setAlias('sensio_labs.page_object_extension.page_factory', $config['factory']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
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
}
