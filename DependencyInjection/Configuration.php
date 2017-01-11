<?php

namespace Alpixel\Bundle\JiraBundle\DependencyInjection;

use Alpixel\Bundle\JiraBundle\Request\BasicAuthentication;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('alpixel_jira');

        $rootNode
            ->children()
                ->scalarNode('base_url')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('base_api')->isRequired()->cannotBeEmpty()->end()
                ->append($this->addAuthConfiguration())
            ->end();

        return $treeBuilder;
    }

    public function addAuthConfiguration()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('auth');

        $node->isRequired()
            ->children()
                ->scalarNode('method')->defaultValue('basic')->end()
                ->arrayNode('parameters')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('authentication_class')
                    ->defaultValue(BasicAuthentication::class)
                    ->beforeNormalization()
                        ->always()
                        ->then(function ($fqcn) {
                            $isValidClass = false;
                            if (class_exists($fqcn)) {
                                $interfaces = class_implements($fqcn);
                                $isValidClass = isset($interfaces['\Alpixel\Bundle\JiraBundle\Request\AuthenticationInterface']);
                            }

                            if (!$isValidClass) {
                                throw new \InvalidArgumentException('The class has been not found or not implement "\Alpixel\Bundle\JiraBundle\Request\AuthenticationInterface"');
                            }
                        })
                    ->end()
                ->end()
            ->end();
        return $node;
    }
}
