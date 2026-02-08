<?php

namespace App\Enums;

enum TaskStatuses: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';


    /**
     * Get all values as array
     * Example: ['pending','completed', 'canceled']
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}