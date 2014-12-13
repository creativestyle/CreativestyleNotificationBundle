<?php

namespace Creativestyle\Bundle\NotificationBundle\EventListener;

use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Bundle\NotificationBundle\Event\NotificationEvent;

class NotificatorListener
{
    protected $notificator;

    public function setNotificator(NotificatorInterface $notificator)
    {
        $this->notificator = $notificator;
    }

    public function notify(NotificationEvent $event)
    {
        $this->notificator->notify($event->getNotification());
    }
}