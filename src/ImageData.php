<?php

declare(strict_types=1);

namespace Yard\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class ImageData extends Data
{
    public function __construct(
        public int $id,
        public string $file,
        public int $width,
        public int $height,
        public int $filesize,
        public string $name,
        public string $url,
        public string $alt,
        public string $caption,
        public string $description,
        public string $mimeType,
        /** @var Collection<int, ImageSizeData> */
        public Collection $sizes,
    ) {
    }

    public static function fromID(int $id): self
    {
        $meta = wp_get_attachment_metadata($id);

        return new self(
            id: $id,
            file: $meta['file'],
            width: $meta['width'],
            height: $meta['height'],
            filesize: $meta['filesize'],
            name: get_post_field('post_name', $id),
            url: wp_get_attachment_url($id),
            alt: get_post_meta($id, '_wp_attachment_image_alt', true),
            caption: get_post_field('post_excerpt', $id),
            description: get_post_field('post_content', $id),
            mimeType: get_post_mime_type($id),
            sizes: ImageSizeData::collect(wp_get_attachment_metadata($id)['sizes'], Collection::class)
        );
    }

    public function alt(string $default = ''): string
    {
        return ! empty($this->alt) ? $this->alt : $default;
    }

    public function url(string $size = 'medium_large'): string
    {
        return wp_get_attachment_image_url($this->id, $size) ?: $this->url;
    }
}
