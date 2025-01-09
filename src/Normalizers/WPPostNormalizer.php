<?php

declare(strict_types=1);

namespace Yard\Data\Normalizers;

use Spatie\LaravelData\Normalizers\Normalizer;

class WPPostNormalizer implements Normalizer
{
	/**
	 * @return array<mixed>|null
	 */
	public function normalize(mixed $value): ?array
	{
		if (! $value instanceof \WP_Post) {
			return null;
		}

		return $value->to_array();
	}
}
