<?php

namespace Alpixel\Bundle\JiraBundle\DependencyInjection;

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
                ->append($this->addAuthParameters())
            ->end();

        return $treeBuilder;
    }

    public function addAuthParameters()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('auth');

        $node->isRequired()
            ->children()
                ->arrayNode('method')
                    ->children()
                        ->arrayNode('basic')
                            ->children()
                                ->scalarNode('username')->end()
                                ->scalarNode('password')->end()
                            ->end()
                        ->end()
                        ->arrayNode('oauth')
                            ->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('key')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->beforeNormalization()
                    ->ifTrue(function ($v) { return (count($v) === 1) ? false : true ; })
                    ->thenInvalid('You must set only one authentification method.')
                ->end()
            ->end();

        return $node;
    }
}
