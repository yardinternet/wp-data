<?php

declare(strict_types=1);

namespace Yard\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Yard\Data\Mappers\UserPrefixMapper;

#[MapInputName(UserPrefixMapper::class)]
class UserData extends Data implements Castable
{
    public function __construct(
        #[MapInputName('ID')]
        public int $id,
        public string $login,
        #[MapInputName('user_pass')]
        public string $password,
        public string $nicename,
        public string $email,
        #[MapInputName('display_name')]
        public string $displayName,
    ) {

    }

    public static function fromUser(\WP_User $user): self
    {
        return new self(
            id: $user->ID,
            login: $user->user_login,
            password: $user->user_pass,
            nicename: $user->user_nicename,
            email: $user->user_email,
            displayName: $user->display_name,
        );
    }

    public static function dataCastUsing(...$args): Cast
    {
        return new class implements Cast {
            public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?UserData
            {
                $user = new \WP_User($value);

                if (0 === $user->ID) {
                    return null;
                }

                return new UserData(
                    id: $user->ID,
                    login: $user->user_login,
                    password: $user->user_pass,
                    nicename: $user->user_nicename,
                    email: $user->user_email,
                    displayName: $user->display_name
                );

            }
        };
    }

    public function can($cap)
    {
        return \user_can($this->id, $cap);
    }

    public function hasRole($role)
    {
        return \user_can($this->id, $role);
    }
}
