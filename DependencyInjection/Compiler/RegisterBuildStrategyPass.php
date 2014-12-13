<?php

namespace Creativestyle\Bundle\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterBuildStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('creativestyle.notification.build.strategy_provider')) {
            return;
        }
        $strategyProvider = $container->getDefinition('creativestyle.notification.build.strategy_provider');

        $taggedServices = $container->findTaggedServiceIds(
            'creativestyle_notification.build_strategy'
        );

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                if (!array_key_exists('type', $tag)) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "type" attribute on "creativestyle_notification.build_strategy" tags.', $id));
                }

                $strategyProvider->addMethodCall(
                    'addBuildStrategy',
                    array($tag['type'], new Reference($id))
                );
            }
        }
    }
}