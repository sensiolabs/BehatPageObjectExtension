<?php

namespace SensioLabs\PageObjectExtension;

use Behat\Behat\Extension\ExtensionInterface;
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

        if (isset($config['pages_namespace'])) {
            $container->setParameter('sensio_labs.page_object_extension.pages_namespace', $config['pages_namespace']);
        }
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                scalarNode('pages_namespace')->
                   defaultValue('\\')->
                end()->
            end();
    }

    /**
     * @return array
     */
    public function getCompilerPasses()
    {
        return array();
    }
}
