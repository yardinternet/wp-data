<?php

declare(strict_types=1);

namespace Yard\Data\Mappers;

use Spatie\LaravelData\Mappers\NameMapper;

class PostPrefixMapper implements NameMapper
{
	public function map(int|string $name): string|int
	{
		if ('postType' === $name) {
			return 'post_type';
		}

		return 'post_' . $name;
	}
}
