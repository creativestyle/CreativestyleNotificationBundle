<?php

namespace Creativestyle\Bundle\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $provider = $this->get('creativestyle.provider.insite_notification');
        
        $notifications = $provider->getArrayUserNotifications($user);

        return new JsonResponse($notifications);
    }

    
}