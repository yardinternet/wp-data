<?php

declare(strict_types=1);

namespace Yard\Data\Traits;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Yard\Data\Attributes\Meta;
use Yard\Data\Attributes\MetaPrefix;
use Yard\Data\CommentData;
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
				$metaValue = $meta->getValue($this->objectID(), $property->name, $this->metaPrefix());
				if (null === $metaValue || null === $propertyTypeName) {
					continue;
				}
				$metaValue = $this->castValue($metaValue, $propertyTypeName);
				$property->setValue($this, $metaValue);
			}
		}
	}

	private function castValue(mixed $value, string $type): mixed
	{
		if (is_a($type, Data::class, true)) {
			return $type::from($value);
		}

		if (is_a($type, \BackedEnum::class, true) && (is_int($value) || is_string($value))) {
			return $type::from($value);
		}

		if (is_a($type, CarbonImmutable::class, true) && is_string($value)) {
			if (CarbonImmutable::canBeCreatedFromFormat($value, 'Ymd')) {
				return CarbonImmutable::createFromFormat('Ymd', $value);
			}
			if (CarbonImmutable::canBeCreatedFromFormat($value, 'Y-m-d H:i:s')) {
				return CarbonImmutable::createFromFormat('Y-m-d H:i:s', $value);
			} else {
				return CarbonImmutable::parse($value);
			}
		}

		return $value;
	}

	private function metaPrefix(): string
	{
		$reflectionClass = new \ReflectionClass($this);
		$metaPrefixAttribute = $reflectionClass->getAttributes(MetaPrefix::class)[0] ?? null;

		return $metaPrefixAttribute?->newInstance()->prefix ?? '';
	}

	private function objectID(): string|int
	{
		if (is_a($this, PostData::class)) {
			return $this->id ?? 0;
		}
		if (is_a($this, TermData::class)) {
			return $this->taxonomy . '_' . $this->id;
		}
		if (is_a($this, CommentData::class)) {
			return 'comment_' . $this->id;
		}

		return 0;
	}
}
