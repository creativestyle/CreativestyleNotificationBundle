<?php

namespace Creativestyle\Bundle\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('creativestyle_notification');

        $rootNode
            ->children()
                ->arrayNode('notification')
                    ->children()
                        ->arrayNode('insite')
                            ->children()
                                ->booleanNode('enable')
                                    ->defaultValue(false)
                                    ->cannotBeEmpty()
                                ->end()
                                ->booleanNode('prerender')
                                    ->defaultValue(false)
                                ->end()
                                ->scalarNode('model_class')
                                ->end()
                                ->arrayNode('templates')
                                    ->useAttributeAsKey('whatever')
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('notificator')
                    ->children()
                        ->arrayNode('database')
                            ->children()
                                ->booleanNode('enable')
                                    ->defaultValue(false)
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('email')
                            ->children()
                                ->booleanNode('enable')
                                    ->defaultValue(false)
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('service')
                                ->end()
                                ->arrayNode('templates')
                                    ->useAttributeAsKey('whatever')
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('builder')
                    ->children()
                        ->scalarNode('strategy_provider')
                            ->defaultValue('creativestyle.notification.build.strategy_provider')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
