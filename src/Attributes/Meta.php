<?php

declare(strict_types=1);

namespace Yard\Data\Attributes;

use Illuminate\Support\Str;

#[\Attribute(\ATTRIBUTE::TARGET_PROPERTY)]
class Meta
{
    public function __construct(private ?string $metaKey = null)
    {
    }

    public function getValue(int $postID, string $metaKey, string $prefix): mixed
    {
        if (isset($this->metaKey)) {
            if ($value = \get_field($this->metaKey, $postID)) {
                return $value;
            } else {
                return null;
            }
        }

        $possibleKeys = [
            $metaKey,
            Str::snake($metaKey),
            $prefix . $metaKey,
        ];

        foreach ($possibleKeys as $key) {
            if ($value = \get_field($key, $postID)) {
                return $value;
            }
        }

        return null;
    }
}
