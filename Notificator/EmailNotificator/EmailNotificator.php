<?php

namespace Creativestyle\Bundle\NotificationBundle\Notificator\EmailNotificator;

use Creativestyle\Component\Notification\Model\NotificationInterface;
use Creativestyle\Component\Notification\Notificator\NotificatorInterface;
use Creativestyle\Component\Notification\Model\SubscriberInterface;

class EmailNotificator implements NotificatorInterface
{
    protected $mailer;
    protected $templateResolver;
    protected $notificationEmail;

    public function __construct(
        $mailer,
        $templateResolver,
        $notificationEmail = 'no-replay@creativestyle.pl'
    ) {
        $this->mailer = $mailer;
        $this->templateResolver = $templateResolver;
        $this->notificationEmail = $notificationEmail;
    }

    public function notify(NotificationInterface $notification)
    {
        if (!$notification->getSubscriber() instanceof SubscriberInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s should implement SubscriberInterface or disable email nootification',
                    gettype($notification->getSubscriber())
                )
            );
        }
        $template = $this->templateResolver->getTemplate($notification->getType());
        $context = array(
            'notification' => $notification,
            'object' => $notification->getObject(),
            'subscriber' => $notification->getSubscriber(),
            'broadcaster' => $notification->getBroadcaster(),
        );

        $subscriber = $notification->getSubscriber();

        $this->mailer->sendEmail(
            $template,
            $context,
            $this->notificationEmail,
            $subscriber->getEmail()
        );
    }
}