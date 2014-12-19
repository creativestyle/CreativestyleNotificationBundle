<?php

namespace Creativestyle\Bundle\NotificationBundle\Template;

use Creativestyle\Component\Notification\Model\NotificationInterface;

class Renderer
{
    protected $twig;

    public function __construct(
        $twig
    ) {
        $this->twig = $twig;
    }

    public function render($templateName, $block, NotificationInterface $notification)
    {
        $context = array(
            'notification' => $notification,
            'object' => $notification->getObject(),
            'subscriber' => $notification->getSubscriber(),
            'broadcaster' => $notification->getBroadcaster(),
        );

        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);

        return $template->renderBlock($block, $context);
    }
}
