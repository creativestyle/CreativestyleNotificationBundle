<?php

namespace Creativestyle\Bundle\NotificationBundle\Notificator\DBNotificator;

use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\NotificationInterface;

class DatabaseNotificator implements NotificatorInterface
{
    protected $messageManager;

    public function __construct($messageManager)
    {
        $this->messageManager = $messageManager;
    }

    public function notify(NotificationInterface $notification)
    {
        $this->messageManager->saveNotification($notification);
    }
}