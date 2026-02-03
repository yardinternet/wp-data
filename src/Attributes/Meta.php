<?php

declare(strict_types=1);

namespace Yard\Data\Attributes;

use Illuminate\Support\Str;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Meta
{
	public function __construct(private ?string $metaKey = null)
	{
	}

	public function getValue(string|int $objectID, string $metaKey, string $prefix): mixed
	{
		if (isset($this->metaKey)) {
			return \get_field($this->metaKey, $objectID);
		}

		$possibleKeys = [
			$metaKey,
			Str::snake($metaKey),
			$prefix . $metaKey,
			$prefix . Str::snake($metaKey),
		];

		foreach ($possibleKeys as $key) {
			$value = \get_field($key, $objectID);
			if (null !== $value) {
				return $value;
			}
		}

		return null;
	}
}
