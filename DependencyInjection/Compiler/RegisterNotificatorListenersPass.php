<?php

namespace Creativestyle\Bundle\NotificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Creativestyle\Bundle\NotificationBundle\NotificationEvents;

class RegisterNotificatorListenersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'creativestyle_notification.notificator'
        );

        //Register listeners for notificator
        foreach ($taggedServices as $id => $tags) {
            $listenerDefinition = $this->getListenerDefinition($id);
            $listenerId = str_replace('.notificator.', '.listener.', $id);
            $container->setDefinition($listenerId, $listenerDefinition);
        }
    }

    protected function getListenerDefinition($notificatorKey)
    {
        $listenerClass = 'Creativestyle\Bundle\NotificationBundle\EventListener\NotificatorListener';

        $definition = new Definition($listenerClass);
        $definition
            ->addMethodCall('setNotificator', array(
                new Reference($notificatorKey)
            ))
            ->addTag(
                'kernel.event_listener',
                array(
                    'event' => NotificationEvents::SEND,
                    'method' => 'notify'
                )
            )
        ;

        return $definition;
    }
}