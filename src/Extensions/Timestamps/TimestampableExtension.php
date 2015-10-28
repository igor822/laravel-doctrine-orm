<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ORM\Extensions\Extension;

class TimestampableExtension implements Extension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader|null            $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null)
    {
        $subscriber = new TimestampableSubscriber();

        $manager->addEventSubscriber($subscriber);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [];
    }
}
