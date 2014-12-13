<?php

namespace Creativestyle\Bundle\NotificationBundle\NotificationDispatcher;

class Dispatcher
{
    protected $builder;
    protected $notificator;

    public function __construct($notificator, $builder)
    {
        $this->notificator = $notificator;
        $this->builder = $builder;
    }

    public function notifyAbout($object, $type, $sender = null)
    {
        $notification = $this->builder->create($object, $type, $sender);
        $this->notificator->notify($notification);
    }
}