<?php

namespace App\Database\Hydration\Strategies;

use DateTime;
use Exception;

class DateTimeStrategy implements StrategyInterface
{

    public function hydrate($value)
    {
        try {
            return new DateTime($value);
        } catch (Exception $e) {
            return new DateTime();
        }
    }
}