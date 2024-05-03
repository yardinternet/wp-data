<?php

declare(strict_types=1);

namespace Yard\Data;

use Carbon\CarbonImmutable;
use ReflectionClass;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Normalizers\ArrayableNormalizer;
use Spatie\LaravelData\Normalizers\ArrayNormalizer;
use Spatie\LaravelData\Normalizers\JsonNormalizer;
use Spatie\LaravelData\Normalizers\ModelNormalizer;
use Spatie\LaravelData\Normalizers\ObjectNormalizer;
use Yard\Data\Attributes\Meta;
use Yard\Data\Attributes\MetaPrefix;
use Yard\Data\Attributes\TaxonomyPrefix;
use Yard\Data\Attributes\Terms;
use Yard\Data\Contracts\PostDataInterface;
use Yard\Data\Enums\PostStatus;
use Yard\Data\Mappers\PostPrefixMapper;
use Yard\Data\Normalizers\WPPostNormalizer;

#[MapInputName(PostPrefixMapper::class)]
class PostData extends Data implements PostDataInterface
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
        public string $postType,
        #[MapInputName('post_name')]
        public string $slug,
    ) {
        $this->loadMeta($id);
        $this->loadTerms($id);
    }

    public static function fromPost(\WP_Post $post): PostData
    {
        return new (self::dataClass($post))(
            id: $post->ID,
            author: UserData::fromUser(get_userdata($post->post_author)),
            title: $post->post_title,
            content: $post->post_content,
            excerpt: $post->post_excerpt,
            status: PostStatus::from($post->post_status),
            date: CarbonImmutable::createFromTimestamp($post->post_date),
            modified: CarbonImmutable::createFromTimestamp($post->post_modified),
            postType: $post->post_type,
            slug: $post->post_name,
        );
    }

    private static function dataClass(\WP_Post $post): string
    {
        $classes = config('yard-data.post_types', []);

        if (array_key_exists($post->post_type, $classes)) {
            return $classes[$post->post_type];
        }

        return self::class;
    }

    private function metaPrefix(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $metaPrefixAttribute = $reflectionClass->getAttributes(MetaPrefix::class)[0] ?? null;

        return $metaPrefixAttribute?->newInstance()->prefix ?? '';
    }

    private function loadMeta(int $id): void
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $metaAttributes = $property->getAttributes(Meta::class);
            foreach ($metaAttributes as $metaAttribute) {
                $meta = $metaAttribute->newInstance();
                $metaValue = $meta->getValue($id, $property->name, $this->metaPrefix());
                if (null !== $metaValue) {
                    $property->setValue($this, $metaValue);
                }
            }
        }
    }

    private function taxonomyPrefix(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $termPrefixAttribute = $reflectionClass->getAttributes(TaxonomyPrefix::class)[0] ?? null;

        return $termPrefixAttribute?->newInstance()->prefix ?? '';
    }

    private function loadTerms(int $id): void
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $termAttributes = $property->getAttributes(Terms::class);
            foreach ($termAttributes as $termAttribute) {
                $term = $termAttribute->newInstance();
                $termValue = $term->getValue($id, $property->name, $this->taxonomyPrefix());
                if (null !== $termValue) {
                    $this->{$property->name} = $termValue;
                }
            }
        }
    }

    public static function normalizers(): array
    {
        return [
            ModelNormalizer::class,
            ArrayableNormalizer::class,
            ObjectNormalizer::class,
            ArrayNormalizer::class,
            JsonNormalizer::class,
            WPPostNormalizer::class,
        ];
    }

    public function id(): int
    {
        return $this->id;
    }

    public function author(): UserData
    {
        return $this->author;
    }

    public function title(): string
    {
        return get_the_title($this->id);
    }

    public function content(): string
    {
        return apply_filters('the_content', get_the_content(null, false, $this->id));
    }

    public function excerpt(): string
    {
        return get_the_excerpt($this->id);
    }

    public function status(): string
    {
        return $this->status->value;
    }

    public function postType(): string
    {
        return $this->postType;
    }

    public function date(string $format = ''): string
    {
        if (empty($format)) {
            $format = \get_option('date_format');
        }

        return \date_i18n($format, $this->date->timestamp);
    }

    public function modified(string $format = ''): string
    {
        if (empty($format)) {
            $format = \get_option('date_format');
        }

        return \date_i18n($format, $this->modified->timestamp);
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function url(): string
    {
        return \get_permalink($this->id);
    }

    public function thumbnail(string $size = 'medium_large'): string
    {
        return \get_the_post_thumbnail_url($this->id, $size) ?: '';
    }
}
