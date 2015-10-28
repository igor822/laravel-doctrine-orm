<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers;

use DateTime;

class TimeResolver
{
    /**
     * @return DateTime
     */
    public function __invoke()
    {
        return DateTime::createFromFormat('H:i:s', date('H:i:s'));
    }
}
