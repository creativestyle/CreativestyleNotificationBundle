<?php

namespace Creativestyle\Bundle\NotificationBundle\Hydrator;

use Doctrine\Common\Persistence\ObjectManager;
use Creativestyle\Component\Notification\Model\ObjectHolderInterface;
use \RuntimeException as HydratorRuntimeException;

class NotificationObjectHydrator
{
    protected $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function hydrateObject(ObjectHolderInterface $subject)
    {
        $repository = $this->getRepository($subject->getObjectClass());

        $object = $repository->find($subject->getObjectId());

        if (!$object) {
            throw new HydratorRuntimeException(
                sprintf(
                    'object of class %s with id: %d can not be hydrated to Object',
                    $subject->getObjectType(),
                    $subject->getId()
                )
            );
        }

        $subject->setObject($object);
    }

    protected function getRepository($class)
    {
        return $this->om->getRepository($class);
    }
}