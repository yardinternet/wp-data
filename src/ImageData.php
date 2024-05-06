<?php

declare(strict_types=1);

namespace Yard\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ImageData extends Data
{
    public function __construct(
        #[MapInputName('meta_value')]
        public int|string $id,
    ) {
    }

    public function alt(string $default = ''): string
    {
        $alt = get_post_meta($this->id, '_wp_attachment_image_alt', true);

        return ! $alt ? $alt : $default;
    }

    public function url(string $size = 'medium_large'): string
    {
        return wp_get_attachment_image_url($this->id, $size) ?: '';
    }
}
