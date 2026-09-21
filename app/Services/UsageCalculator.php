<?php

namespace App\Services;

use Carbon\Carbon;

class UsageCalculator
{
    public static function years($createdAt)
    {
        return Carbon::parse($createdAt)->diffInYears(now());
    }
}
