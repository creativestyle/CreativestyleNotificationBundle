<?php

namespace Creativestyle\Bundle\NotificationBundle\Provider;

use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\SubscriberInterface;
use Creativestyle\Component\Notification\Model\InsiteNotification;

class InsiteProvider
{
    protected $renderer;
    protected $templateResolver;
    protected $repository;
    protected $hydrator;

    public function __construct(
        $renderer,
        $templateResolver,
        $repository,
        $hydrator
    ) {
        $this->renderer = $renderer;
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
        $templateName = $this->templateResolver->getTemplate($notification->getType());
        $template = $this->templateResolver->getTemplate($notification->getType());
                
        $title = $this->renderer->render($template, 'subject', $notification);
        $textContent = $this->renderer->render($template, 'body_text', $notification);
        $htmlContent = $this->renderer->render($template, 'body_html', $notification);
        $link = $this->renderer->render($template, 'link', $notification);
        $imageSrc = $this->renderer->render($template, 'image_src', $notification);

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