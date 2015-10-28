<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers;

use Carbon\Carbon;

class CarbonResolver
{
    /**
     * @return DateTime
     */
    public function __invoke()
    {
        return Carbon::now();
    }
}
