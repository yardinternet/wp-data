<?php

declare(strict_types=1);

namespace Yard\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Yard\Data\Traits\HasMeta;

/** @phpstan-consistent-constructor */
class TermData extends Data
{
	use HasMeta;

	public const CACHE_GROUP = 'yard_term_data';

	public function __construct(
		#[MapInputName('term_id')]
		public int $id,
		public string $name,
		public string $slug,
		public string $taxonomy,
		public ?string $description = null,
	) {
		$this->loadMeta();
	}

	public static function fromTerm(\WP_Term $term): TermData
	{
		wp_cache_add_non_persistent_groups([self::CACHE_GROUP]);

		$cachedTermData = wp_cache_get($term->term_id, self::CACHE_GROUP, false, $found);
		if ($found && $cachedTermData instanceof TermData) {
			return $cachedTermData;
		}

		$termData = new static(
			id: $term->term_id,
			name: $term->name,
			slug: $term->slug,
			taxonomy: $term->taxonomy,
			description: $term->description,
		);
		wp_cache_set($term->term_id, $termData, self::CACHE_GROUP);

		return $termData;
	}
}
