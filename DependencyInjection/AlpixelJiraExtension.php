<?php

namespace Alpixel\Bundle\JiraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AlpixelJiraExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('alpixel_jira.auth.method', $config['auth']['method']);
        $container->setParameter('alpixel_jira.auth.parameters', $config['auth']['parameters']);
        $container->setParameter('alpixel_jira.auth.authentication_class', $config['auth']['authentication_class']);
        $container->setParameter('alpixel_jira.base_url', $config['base_url']);
        $container->setParameter('alpixel_jira.base_api', $config['base_api']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
