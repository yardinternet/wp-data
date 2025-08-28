<?php

declare(strict_types=1);

namespace Yard\Data\Traits;

use Spatie\LaravelData\Data;
use Yard\Data\Attributes\Meta;
use Yard\Data\Attributes\MetaPrefix;
use Yard\Data\PostData;
use Yard\Data\TermData;

trait HasMeta
{
	private function loadMeta(): void
	{
		$reflectionClass = new \ReflectionClass($this);
		$properties = $reflectionClass->getProperties();
		foreach ($properties as $property) {
			$propertyType = $property->getType();
			$propertyTypeName = null;
			if ($propertyType instanceof \ReflectionNamedType) {
				$propertyTypeName = $propertyType->getName();
			}
			$metaAttributes = $property->getAttributes(Meta::class);
			foreach ($metaAttributes as $metaAttribute) {
				$meta = $metaAttribute->newInstance();
				$metaValue = $meta->getValue($this->postID(), $property->name, $this->metaPrefix());
				if (null !== $metaValue && null !== $propertyTypeName) {
					if (is_a($propertyTypeName, Data::class, true)) {
						$metaValue = $propertyTypeName::from($metaValue);
					} elseif (is_a($propertyTypeName, \BackedEnum::class, true) && (is_int($metaValue) || is_string($metaValue))) {
						$metaValue = $propertyTypeName::from($metaValue);
					}
					$property->setValue($this, $metaValue);
				}
			}
		}
	}

	private function metaPrefix(): string
	{
		$reflectionClass = new \ReflectionClass($this);
		$metaPrefixAttribute = $reflectionClass->getAttributes(MetaPrefix::class)[0] ?? null;

		return $metaPrefixAttribute?->newInstance()->prefix ?? '';
	}

	private function postID(): string|int
	{
		if (is_a($this, PostData::class)) {
			return $this->id ?? 0;
		}
		if (is_a($this, TermData::class)) {
			return $this->taxonomy . '_' . $this->id;
		}

		return 0;
	}
}
