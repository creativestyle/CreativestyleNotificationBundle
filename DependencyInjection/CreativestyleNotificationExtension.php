<?php

namespace Creativestyle\Bundle\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Creativestyle\Bundle\NotificationBundle\EventListener\NotificatorListener;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Creativestyle\Bundle\NotificationBundle\NotificationEvents;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CreativestyleNotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $container->setParameter(
            'creativestyle.notificator.email.sender_name',
            $config['notificator']['email']['sender_name']
        );

        $container->setParameter(
            'creativestyle.notificator.email.sender_email',
            $config['notificator']['email']['sender_email']
        );

        $container->setParameter(
            'creativestyle_notification.model.insite_notification.class',
            $config['notification']['insite']['model_class']
        );

        $this->createServices($container, $config);
    }

    protected function createServices(ContainerBuilder $container, $config)
    {   
        if ($config['notificator']['database']['enable']) {
            $this->createDBNotificator($container, $config);
        }

        if ($config['notificator']['email']['enable']) {
            $this->createEmailNotificator($container, $config);
        }

        if ($config['notification']['insite']['enable']) {
            if (!$config['notificator']['database']['enable']) {
                throw new \RuntimeException('db notificator should be enabled');
            }
            $this->createInsiteNotificator($container, $config);
        }

        $strategyProviderKey = isset($config['builder']['strategy_provider']) ? $config['builder']['strategy_provider'] : 'creativestyle.notification.build.strategy_provider';
        if ($strategyProviderKey == 'creativestyle.notification.build.strategy_provider') {
            $this->createStrategyProvider($container, $strategyProviderKey);
        }

        $this->createBuilder($container, $strategyProviderKey);
    }

    protected function createDBNotificator($container, $config)
    {
        $dbNotificatorKey = 'creativestyle_notification.notificator.db_notificator';
        $container->setDefinition(
            $dbNotificatorKey,
            $this->getDBNotificatorDefinition($container, $config['notification']['insite']['enable'])
        );

//        $container->setDefinition(
//            'creativestyle_notification.listener.db_notificator',
//            $this->getListenerDefinition(
//                $dbNotificatorKey
//            )
//        );
    }

    protected function createEmailNotificator($container, $config)
    {
        if (isset($config['notificator']['email']['service'])) {
            $emailNotificatorKey = $config['notificator']['email']['service'];
        } else {
            $emailNotificatorKey = 'creativestyle_notification.notificator.email_notificator';

            $templateResolverKey = 'creativestyle.notificator.email.template_resolver';
            $templates = $config['notificator']['email']['templates'];

            $container->setDefinition(
                $templateResolverKey,
                $this->getTemplateResolverDefinition($container, $templateResolverKey, $templates)
            );

            $container->setDefinition(
                $emailNotificatorKey,
                $this->getEmailNotificatorDefinition($container, $templateResolverKey)
            );
        }
        
//        $container->setDefinition(
//            'creativestyle_notification.listener.email_notificator',
//            $this->getListenerDefinition(
//                $emailNotificatorKey
//            )
//        );
    }

    protected function createInsiteNotificator($container, $config)
    {
        $insiteNotifyModel = $config['notification']['insite']['model_class'];

        $container->setDefinition(
            'creativestyle_notification.factory.insite_notification',
            $this->getInsiteNotificationFactoryDefinition($insiteNotifyModel, $container)
        );

        $templateResolverKey = 'creativestyle.notificator.insite.template_resolver';
        $templates = $config['notification']['insite']['templates'];

        $container->setDefinition(
            $templateResolverKey,
            $this->getTemplateResolverDefinition($container, $templateResolverKey, $templates)
        );

        if (!$config['notification']['insite']['prerender']) {
            $container->setDefinition(
                'creativestyle.provider.insite_notification',
                $this->getInsiteNotificationProviderDefinition($container, $templateResolverKey)
            );
        } else {
            $container->setDefinition(
                'creativestyle.provider.insite_notification',
                $this->getInsitePrerenderedNotificationProviderDefinition($container, $templateResolverKey)
            );
        }
        
    }

    protected function getInsiteNotificationProviderDefinition($container, $templateResolverKey)
    {
        $emailNotificatorClass = $container->getParameter('creativestyle.provider.insite_notification.class');
        $definition = new Definition($emailNotificatorClass);
        $definition
            ->addArgument(new Reference('creativestyle_notification.template.renderer'))
            ->addArgument(new Reference($templateResolverKey))
            ->addArgument(new Reference('creativestyle.repository.notification'))
            ->addArgument(new Reference('creativestyle.object_hydrator'))
        ;

        return $definition;
    }

    protected function getInsitePrerenderedNotificationProviderDefinition($container, $templateResolverKey)
    {
        $emailNotificatorClass = $container->getParameter('creativestyle.provider.prerender.insite_notification.class');
        $definition = new Definition($emailNotificatorClass);
        $definition
            ->addArgument(new Reference('creativestyle.repository.insite_notification'))
        ;

        return $definition;
    }


    protected function getDBNotificatorDefinition($container, $enableInsite)
    {
        $dbNotificatorClass = $container->getParameter('creativestyle_notification.notificator.db_notificator.class');
        $definition = new Definition($dbNotificatorClass);
        $definition
            ->addArgument(new Reference('creativestyle_notification.manager.notification_manager'))
            ->addTag('creativestyle_notification.notificator')
        ;

        if ($enableInsite) {
            $definition
                ->addArgument(new Reference('creativestyle_notification.factory.insite_notification'))
            ;
        }
        
        return $definition;
    }

    protected function getTemplateResolverDefinition($container, $templateResolverKey, $templates)
    {
        $templateResolverClass = $container->getParameter('creativestyle.notificator.email.template_resolver.class');
        $definition = new Definition($templateResolverClass, array($templates));

        return $definition;
    }

    protected function getEmailNotificatorDefinition($container, $templateResolverKey)
    {
        $emailNotificatorClass = $container->getParameter('creativestyle_notification.notificator.email_notificator.class');
        $definition = new Definition($emailNotificatorClass);
        $definition
            ->addArgument(new Reference('creativestyle_notification.mailer'))
            ->addArgument(new Reference($templateResolverKey))
            ->addArgument($container->getParameter('creativestyle.notificator.email.sender_email'))
            ->addTag('creativestyle_notification.notificator')
        ;

        return $definition;
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

    protected function createStrategyProvider($container, $strategyProviderKey)
    {   
        $container->setDefinition(
            $strategyProviderKey,
            $this->getStrategyProviderDefinition(
                $container
            )
        );
    }

    protected function getStrategyProviderDefinition($container)
    {
        $builderClass = $container
            ->getParameter('creativestyle_notification.builder.strategy_provider.class');
        $definition = new Definition($builderClass);

        return $definition;
    }

    protected function createBuilder($container, $strategyProviderKey)
    {   
        $container->setDefinition(
            'creativestyle_notification.builder.notification_builder',
            $this->getBuilderDefinition(
                $strategyProviderKey
            )
        );
    }

    protected function getBuilderDefinition($strategyProviderKey)
    {
        $builderClass = 'Creativestyle\Component\Notification\Builder\NotificationBuilder';
        $definition = new Definition($builderClass);
        $definition
            ->addArgument(new Reference($strategyProviderKey))
        ;

        return $definition;
    }

    private function getInsiteNotificationFactoryDefinition($modelClass, Container $container)
    {
        $factoryClass = $container->getParameter('creativestyle_notification.factory.insite_notification.class');
        $definition = new Definition($factoryClass);
        $definition
            ->addArgument($modelClass)
            ->addArgument(new Reference('creativestyle_notification.template.renderer'))
            ->addArgument(new Reference('creativestyle.notificator.insite.template_resolver'))
        ;

        return $definition;
    }
}
