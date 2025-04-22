<?php

declare(strict_types=1);

namespace Yard\Data;

use Carbon\CarbonImmutable;
use Corcel\Model\Post;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCastable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Normalizers\ArrayableNormalizer;
use Spatie\LaravelData\Normalizers\ArrayNormalizer;
use Spatie\LaravelData\Normalizers\JsonNormalizer;
use Spatie\LaravelData\Normalizers\ModelNormalizer;
use Spatie\LaravelData\Normalizers\Normalizer;
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
		public ?int $id,
		#[WithCastable(UserData::class)]
		public ?UserData $author,
		public string $title,
		public string $content,
		public string $excerpt,
		public PostStatus $status,
		public ?CarbonImmutable $date,
		public ?CarbonImmutable $modified,
		public string $postType,
		#[MapInputName('post_name')]
		public string $slug,
		public ?ImageData $thumbnail,
	) {
		if (null !== $id) {
			$this->loadMeta($id);
			$this->loadTerms($id);
		}
	}

	public static function fromPost(\WP_Post $post): PostData
	{
		return new (self::dataClass($post->post_type))(
			id: $post->ID,
			author: false !== get_userdata((int) $post->post_author) ? UserData::fromUser(get_userdata((int) $post->post_author)) : null,
			title: $post->post_title,
			content: $post->post_content,
			excerpt: $post->post_excerpt,
			status: PostStatus::from($post->post_status),
			date: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $post->post_date),
			modified: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $post->post_modified),
			postType: $post->post_type,
			slug: $post->post_name,
			thumbnail: get_post_thumbnail_id($post->ID) ? new ImageData(get_post_thumbnail_id($post->ID)) : null,
		);
	}

	public static function fromCorcel(Post $post): PostData
	{
		return new (self::dataClass($post->post_type))(
			id: $post->ID,
			author: false !== get_userdata($post->post_author) ? UserData::fromUser(get_userdata($post->post_author)) : null,
			title: $post->post_title,
			content: $post->post_content,
			excerpt: $post->post_excerpt,
			status: PostStatus::from($post->post_status),
			date: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $post->post_date),
			modified: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $post->post_modified),
			postType: $post->post_type,
			slug: $post->post_name,
			thumbnail: get_post_thumbnail_id($post->ID) ? new ImageData(get_post_thumbnail_id($post->ID)) : null,
		);
	}

	/**
	 * @return class-string<PostData>
	 */
	private static function dataClass(string $postType): string
	{
		$classes = config('yard-data.post_types', []);

		if (is_array($classes) && array_key_exists($postType, $classes)) {
			return $classes[$postType];
		}

		$classFQN = get_all_post_type_supports($postType)['data-class'][0]['classFQN'] ?? null;

		if (null === $classFQN) {
			return static::class;
		}
		
		if (! class_exists($classFQN)) {
			throw new RuntimeException(sprintf('The class "%s" does not exist or is not autoloaded.', $classFQN));
		}

		if (! is_a($classFQN, PostData::class, true)) {
			throw new RuntimeException(sprintf('The class "%s" must extend %s.', $classFQN, PostData::class));
		}

		return $classFQN;
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
			$propertyType = $property->getType();
			$propertyTypeName = null;
			if ($propertyType instanceof ReflectionNamedType) {
				$propertyTypeName = $propertyType->getName();
			}
			$metaAttributes = $property->getAttributes(Meta::class);
			foreach ($metaAttributes as $metaAttribute) {
				$meta = $metaAttribute->newInstance();
				$metaValue = $meta->getValue($id, $property->name, $this->metaPrefix());
				if (null !== $metaValue && null !== $propertyTypeName) {
					if ('Spatie\LaravelData\Data' === $propertyTypeName || is_subclass_of($propertyTypeName, 'Spatie\LaravelData\Data')) {
						$metaValue = $propertyTypeName::from($metaValue);
					}
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
					$property->setValue($this, $termValue);
				} else {
					$property->setValue($this, collect());
				}
			}
		}
	}

	/**
	 * @return array<int, class-string<Normalizer>>
	 */
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

	public function id(): ?int
	{
		return $this->id;
	}

	public function author(): ?UserData
	{
		return $this->author;
	}

	public function title(): string
	{
		if (null === $this->id) {
			return '';
		}

		return get_the_title($this->id);
	}

	public function content(): string
	{
		return apply_filters('the_content', get_the_content(null, false, $this->id));
	}

	public function excerpt(int $count = 0): string
	{
		if (0 === $count) {
			return get_the_excerpt($this->id);
		}

		add_filter('excerpt_length', fn () => $count, PHP_INT_MAX);

		$excerpt = get_the_excerpt($this->id);

		remove_all_filters('excerpt_length', PHP_INT_MAX);

		return $excerpt;
	}

	public function status(): string
	{
		return $this->status->value;
	}

	public function postType(): string
	{
		return $this->postType;
	}

	protected function defaultDateFormat(): string
	{
		$dateFormat = \get_option('date_format');

		return is_string($dateFormat) ? $dateFormat : '';
	}

	public function date(string $format = ''): string
	{
		if (null === $this->date) {
			return '';
		}

		if ('' === $format) {
			$format = $this->defaultDateFormat();
		}

		return \date_i18n($format, (int) $this->date->timestamp);
	}

	public function modified(string $format = ''): string
	{
		if (null === $this->modified) {
			return '';
		}

		if ('' === $format) {
			$format = $this->defaultDateFormat();
		}

		return \date_i18n($format, (int) $this->modified->timestamp);
	}

	public function slug(): string
	{
		return $this->slug;
	}

	public function thumbnail(): ?ImageData
	{
		return $this->thumbnail;
	}

	public function hasThumbnail(): bool
	{
		return null !== $this->thumbnail;
	}

	public function url(): string
	{
		if (null === $this->id) {
			return '';
		}

		return \get_permalink($this->id) ?: '';
	}
}
