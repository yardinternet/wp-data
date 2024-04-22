<?php

declare(strict_types=1);

namespace Yard\Data\Mappers;

use Spatie\LaravelData\Mappers\NameMapper;

class UserPrefixMapper implements NameMapper
{
    public function map(int|string $name): string|int
    {
        return 'user_' . $name;
    }
}
