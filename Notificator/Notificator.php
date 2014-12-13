<?php

namespace Creativestyle\Bundle\NotificationBundle\Notificator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Bundle\NotificationBundle\NotificationEvents;
use Creativestyle\Bundle\NotificationBundle\Event\NotificationEvent;

class Notificator
{
    /**
     * The event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(
        EventDispatcherInterface $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    public function notify(NotificationInterface $notification)
    {
        $this->dispatcher->dispatch(
            NotificationEvents::PRE_SEND,
            new NotificationEvent($notification)
        );

        $this->dispatcher->dispatch(
            NotificationEvents::SEND,
            new NotificationEvent($notification)
        );

        $this->dispatcher->dispatch(
            NotificationEvents::POST_SEND,
            new NotificationEvent($notification)
        );
    }
}