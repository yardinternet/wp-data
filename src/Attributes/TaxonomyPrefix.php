<?php

declare(strict_types=1);

namespace Yard\Data\Attributes;

use Illuminate\Support\Str;

#[\Attribute(\Attribute::TARGET_CLASS)]
class TaxonomyPrefix
{
    public function __construct(
        public string $prefix
    ) {
        if (! Str::endsWith($prefix, '_')) {
            $prefix .= '_';
        }
        $this->prefix = $prefix;
    }
}
