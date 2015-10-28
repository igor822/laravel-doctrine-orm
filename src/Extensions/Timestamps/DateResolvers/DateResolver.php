<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers;

use DateTime;

class DateResolver
{
    /**
     * @return DateTime
     */
    public function __invoke()
    {
        return DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
    }
}
