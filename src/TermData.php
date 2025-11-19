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
		return new static(
			id: $term->term_id,
			name: $term->name,
			slug: $term->slug,
			taxonomy: $term->taxonomy,
			description: $term->description,
		);
	}
}
