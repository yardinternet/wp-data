<?php

declare(strict_types=1);

namespace Yard\Data;

use Spatie\LaravelData\Data;

class ImageSizeData extends Data
{
    public function __construct(
        public string $file,
        public int $width,
        public int $height,
        public int $filesize,
    ) {
    }
}
