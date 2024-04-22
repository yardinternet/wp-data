<?php

declare(strict_types=1);

namespace Yard\Data;

use Carbon\CarbonImmutable;
use ReflectionClass;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Yard\Data\Attributes\Meta;
use Yard\Data\Attributes\Terms;
use Yard\Data\Enums\PostStatus;
use Yard\Data\Mappers\PostPrefixMapper;

#[MapInputName(PostPrefixMapper::class)]
class PostData extends Data
{
    public function __construct(
        #[MapInputName('ID')]
        public int $id,
        #[WithCastable(UserData::class)]
        public ?UserData $author,
        public string $title,
        public string $content,
        public string $excerpt,
        public PostStatus $status,
        public CarbonImmutable $date,
        public CarbonImmutable $modified,
        public string $type,
        #[MapInputName('post_name')]
        public string $slug,
    ) {
        $this->loadMeta($id);
        $this->loadTerms($id);
    }

    private function loadMeta(int $id): void
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $metaAttributes = $property->getAttributes(Meta::class);
            foreach ($metaAttributes as $metaAttribute) {
                $meta = $metaAttribute->newInstance();
                $metaValue = $meta->getValue($id, $this->type, $property->name);
                if (null !== $metaValue) {
                    $this->{$property->name} = $metaValue;
                }
            }
        }
    }

    private function loadTerms(int $id): void
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $termAttributes = $property->getAttributes(Terms::class);
            foreach ($termAttributes as $termAttribute) {
                $term = $termAttribute->newInstance();
                $termValue = $term->getValue($id, $this->type, $property->name);
                if (null !== $termValue) {
                    $this->{$property->name} = $termValue;
                }
            }
        }
    }

    public static function fromPost(\WP_Post $post): static
    {
        return new static(
            id: $post->ID,
            author: UserData::from(get_user_by('id', $post->post_author)),
            title: get_the_title($post->ID),
            content: apply_filters('the_content', get_the_content(null, false, $post->ID)),
            excerpt: get_the_excerpt($post->ID),
            status: PostStatus::from($post->post_status),
            date: CarbonImmutable::parse($post->post_date),
            modified: CarbonImmutable::parse($post->post_modified),
            type: $post->post_type,
            slug: $post->post_name,
        );
    }

    public function url(): string
    {
        return \get_permalink($this->id);
    }

    public function formatDate(string $format = ''): string
    {
        if (empty($format)) {
            $format = \get_option('date_format');
        }

        return \date_i18n($format, $this->date->timestamp);
    }

    public function thumbnail(string $size = 'medium_large'): string
    {
        return \get_the_post_thumbnail_url($this->id, $size) ?: '';
    }
}
