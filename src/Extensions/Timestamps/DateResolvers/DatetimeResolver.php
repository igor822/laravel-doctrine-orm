<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers;

use DateTime;

class DatetimeResolver
{
    /**
     * @return DateTime
     */
    public function __invoke()
    {
        return new DateTime();
    }
}
