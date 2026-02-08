<?php

namespace App\Enums;

enum TaskAllowedFilters: string
{
    case ASSIGNEDUSER = 'assigned_to';
    case DUE_DATE_FROM = 'due_date_from';
    case DUE_DATE_TO = 'due_date_to';
    case STATUS = 'status';


    /**
     * Get all values as array
     * Example: ['pending','completed', 'canceled']
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}