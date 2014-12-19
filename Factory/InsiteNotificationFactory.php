<?php

namespace Creativestyle\Bundle\NotificationBundle\Factory;

use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Component\Notification\Model\InsiteNotification;

class InsiteNotificationFactory
{
    protected $renderer;
    protected $templateResolver;
    protected $class;

    public function __construct(
        $class,
        $renderer,
        $templateResolver
    ) {
        $this->class = $class;
        $this->renderer = $renderer;
        $this->templateResolver = $templateResolver;
    }

    public function createNew(NotificationInterface $notification)
    {
        $template = $this->templateResolver->getTemplate($notification->getType());

        $insiteNotification = new $this->class();
        $insiteNotification
            ->setSubscriber($notification->getSubscriber())
            ->setNotification($notification)
            ->setTitle($this->renderer->render($template, 'subject', $notification))
            ->setTextContent($this->renderer->render($template, 'body_text', $notification))
            ->setHtmlContent($this->renderer->render($template, 'body_html', $notification))
            ->setLink($this->renderer->render($template, 'link', $notification))
            ->setImageSrc($this->renderer->render($template, 'image_src', $notification))
            ->setIsRead($notification->getIsRead())
            ->setDate($notification->getCreatedAt())
        ;

        $notification->setInsiteNotification($insiteNotification);

        return $insiteNotification;
    }
}