<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers;

use DateTime;

class DateResolverFactory
{
    /**
     * @param $type
     *
     * @return DateTime
     */
    public function __invoke($type)
    {
        $class   = __NAMESPACE__ . '\\' . ucfirst($type) . 'Resolver';
        $resolve = new $class;

        return $resolve();
    }
}
