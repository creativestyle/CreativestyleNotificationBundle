<?php

namespace Creativestyle\Bundle\NotificationBundle\Notificator\DBNotificator;

use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\NotificationInterface;

class DatabaseNotificator implements NotificatorInterface
{
    protected $manager;
    protected $insiteNotificationFactory;

    public function __construct($manager, $insiteNotificationFactory = null)
    {
        $this->manager = $manager;
        $this->insiteNotificationFactory = $insiteNotificationFactory;
    }

    public function notify(NotificationInterface $notification)
    {
        if ($this->insiteNotificationFactory !== null) {
            $this->insiteNotificationFactory->createNew($notification);
        }

        $this->manager->saveNotification($notification);
    }
}