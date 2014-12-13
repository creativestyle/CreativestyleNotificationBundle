<?php

namespace Creativestyle\Bundle\NotificationBundle\Provider;

use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\SubscriberInterface;
use Creativestyle\Component\Notification\Model\InsiteNotification;

class InsiteProvider
{
    protected $twig;
    protected $templateResolver;
    protected $repository;
    protected $hydrator;

    public function __construct(
        $twig,
        $templateResolver,
        $repository,
        $hydrator
    ) {
        $this->twig = $twig;
        $this->templateResolver = $templateResolver;
        $this->repository = $repository;
        $this->hydrator = $hydrator;
    }

    public function getUserNotifications($user)
    {
        $notifications = $this->findAll($user);

        $collection = array();

        foreach ($notifications as $notify) {
            $this->hydrateObject($notify);
            $collection[] = $this->transformToInsiteNotification($notify);
        }

        return $collection;
    }

    public function getArrayUserNotifications($user)
    {
        $notifications = $this->findAll($user);

        $collection = array();

        foreach ($notifications as $notify) {
            $this->hydrateObject($notify);
            $collection[] = $this->transformToInsiteNotification($notify)->getArrayRepresentation();
        }

        return $collection;
    }

    protected function transformToInsiteNotification(NotificationInterface $notification)
    {
        $context = array(
            'notification' => $notification,
            'object' => $notification->getObject(),
            'subscriber' => $notification->getSubscriber(),
            'broadcaster' => $notification->getBroadcaster(),
        );

        $templateName = $this->templateResolver->getTemplate($notification->getType());
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);

        $title = $template->renderBlock('subject', $context);
        $textContent = $template->renderBlock('body_text', $context);
        $htmlContent = $template->renderBlock('body_html', $context);
        $link = $template->renderBlock('link', $context);
        $imageSrc = $template->renderBlock('image_src', $context);

        $insiteNotification = new InsiteNotification();
        $insiteNotification
            ->setId($notification->getId())
            ->setTitle($title)
            ->setTextContent($textContent)
            ->setHtmlContent($htmlContent)
            ->setLink($link)
            ->setIsRead($notification->getIsRead())
            ->setDate($notification->getCreatedAt())
            ->setImageSrc($imageSrc)
        ;

        return $insiteNotification;
    }

    protected function findAll($user)
    {
        return $this->repository
            ->findBy(
                array(
                    'subscriber' => $user,
                ),
                array(
                    'id' => 'desc'
                )
            );
    }

    protected function hydrateObject($notification)
    {
        $this->hydrator->hydrateObject($notification);
    }
}