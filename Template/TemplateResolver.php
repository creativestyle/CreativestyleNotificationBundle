<?php

namespace Creativestyle\Bundle\NotificationBundle\Template;

class TemplateResolver
{
    protected $templates;

    public function __construct($templates = array())
    {   
        $this->templates = array(
            'baseNotification' => 'CreativestyleNotificationBundle:Email:baseNotification.html.twig',
        );
        $this->templates = array_merge($this->templates, $templates);
    }

    public function getTemplate($type)
    {
        if (!array_key_exists($type, $this->templates)) {
            throw new \InvalidArgumentException(
                sprintf('%s could not been resolved to any template', $type)
            );
        }

        return $this->templates[$type];
    }
}