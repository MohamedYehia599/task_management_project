<?php

namespace App\Enums;

enum UserRoles: string
{
    case MANAGER = 'manager';
    case USER = 'user';
}