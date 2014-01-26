<?php

namespace SensioLabs\Behat\PageObjectExtension;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use SensioLabs\Behat\PageObjectExtension\Compiler\NamespacesPass;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Behat\MinkExtension\Extension as MinkExtension;

class Extension implements ExtensionInterface
{
    const PAGE_OBJECT_FACTORY = 'sensio_labs.page_object_extension.page_factory';
    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadPageObjectFactory($container, $config);
        $this->loadContextInitializer($container);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('namespaces')
                    ->children()
                        ->scalarNode('page')->isRequired()->end()
                        ->scalarNode('element')->isRequired()->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    private function loadPageObjectFactory(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('SensioLabs\Behat\PageObjectExtension\Context\PageFactory', array(
            new Reference(MinkExtension::MINK_ID),
            '%mink.parameters%'
        ));

        $definition->addMethodCall('setPageNamespace', array($config['namespaces']['page']));
        $definition->addMethodCall('setElementNamespace', array($config['namespaces']['element']));

        $container->setDefinition(self::PAGE_OBJECT_FACTORY, $definition);
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('SensioLabs\Behat\PageObjectExtension\Context\Initializer\PageObjectAwareInitializer', array(
            new Reference(self::PAGE_OBJECT_FACTORY)
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('sensio_labs.page_object_extension.context.initializer', $definition);
    }
}
