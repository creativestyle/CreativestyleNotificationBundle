<?php

namespace Creativestyle\Bundle\NotificationBundle\Provider;

use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\SubscriberInterface;
use Creativestyle\Component\Notification\Model\InsiteNotification;

class PrerenderInsiteProvider
{
    protected $repository;

    public function __construct(
        $repository
    ) {
        $this->repository = $repository;
    }

    public function getUserNotifications($user)
    {
        return $this->findAll($user);
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
            )
        ;
    }
}