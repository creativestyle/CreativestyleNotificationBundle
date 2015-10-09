<?php

namespace Creativestyle\Bundle\NotificationBundle;

use Creativestyle\Bundle\NotificationBundle\DependencyInjection\Compiler\RegisterNotificatorListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Creativestyle\Bundle\NotificationBundle\DependencyInjection\Compiler\RegisterBuildStrategyPass;

class CreativestyleNotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Creativestyle\Component\Notification\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        $container->addCompilerPass(new RegisterBuildStrategyPass());
        $container->addCompilerPass(new RegisterNotificatorListenersPass());
    }
}
