<?php

declare(strict_types=1);

namespace Yard\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class TermData extends Data
{
	#[MapInputName('term_id')]
	public int $id;
	public string $name;
	public string $slug;

	public static function fromTerm(\WP_Term $term): TermData
	{
		return self::from(
			$term->to_array()
		);
	}
}
