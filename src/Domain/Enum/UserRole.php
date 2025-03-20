<?php

namespace App\Domain\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
    case MODERATOR = 'ROLE_MODERATOR';
}
