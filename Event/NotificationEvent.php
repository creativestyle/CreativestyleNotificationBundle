<?php

namespace Creativestyle\Bundle\NotificationBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Creativestyle\Component\Notification\Model\NotificationInterface;

/**
 * @author Jakub Kanclerz <kuba.kanclerz@creativestyle.pl>
 */
class NotificationEvent extends Event
{
    protected $notification;
    
    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification()
    {
        return $this->notification;
    }
}