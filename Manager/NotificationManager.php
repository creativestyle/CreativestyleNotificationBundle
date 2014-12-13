<?php

namespace Creativestyle\Bundle\NotificationBundle\Manager;

use Creativestyle\Component\Notification\Model\NotificationInterface;

class NotificationManager
{
    protected $om;

    public function __construct($om)
    {
        $this->om = $om;
    }

    public function saveNotification(NotificationInterface $notification)
    {
        $this->om->persist($notification);
        $this->om->flush();
    }
}