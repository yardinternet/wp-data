<?php

declare(strict_types=1);

namespace Yard\Data\Attributes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Yard\Data\TermData;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Terms
{
	public function __construct(private ?string $taxonomy = null, private string $dataClass = TermData::class)
	{
	}

	public function getValue(int $postID, string $taxonomy, string $prefix): mixed
	{
		if (isset($this->taxonomy)) {
			$possibleTaxonomies = [
				$this->taxonomy,
			];
		} else {
			$possibleTaxonomies = [
				$taxonomy,
				$prefix . $taxonomy,
				$prefix . Str::snake($taxonomy),
				Str::singular($prefix . $taxonomy),
				Str::snake($taxonomy),
				Str::singular(Str::snake($taxonomy)),
				Str::singular($taxonomy),
			];
		}

		foreach ($possibleTaxonomies as $tax) {
			if (taxonomy_exists($tax)) {
				$terms = get_the_terms($postID, $tax);

				if (! is_array($terms)) {
					return null;
				}

				return $this->dataClass::collect($terms, Collection::class);
			}
		}

		return null;
	}
}
