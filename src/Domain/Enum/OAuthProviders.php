<?php

namespace App\Domain\Enum;

enum OAuthProviders: string
{
    case GOOGLE = 'google';
    case GITHUB = 'github';

    public static function values(): array
    {
        return array_map(fn(self $oAuthProviders) => $oAuthProviders->value, self::cases());
    }
}
