<?php
namespace App\Traits;
trait AllowedFiltersTrait
{
    /**
     * Extract allowed filters from validated request data
     */
    protected function getAllowedFilters(array $validated, array $allowedFilters): array
    {
        return array_intersect_key($validated, array_flip($allowedFilters));
    }
    
}