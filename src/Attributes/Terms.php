<?php

declare(strict_types=1);

namespace Yard\Data\Attributes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Yard\Data\TermData;

#[\Attribute(\ATTRIBUTE::TARGET_PROPERTY)]
class Terms
{
    public function __construct(private ?string $taxonomy = null)
    {
    }

    public function getValue(int $postID, string $type, $taxonomy = null): mixed
    {
        if (isset($this->taxonomy)) {
            $terms = get_the_terms($postID, $this->taxonomy);

            if (is_wp_error($terms)) {
                return null;
            }

            return TermData::collect($terms, Collection::class);
        }

        $possibleTaxonomies = [
            $taxonomy,
            $type . '_' . $taxonomy,
            Str::singular($type . '_' . $taxonomy),
            Str::snake($taxonomy),
            Str::singular($taxonomy),
        ];

        foreach ($possibleTaxonomies as $tax) {
            if (taxonomy_exists($tax)) {
                return TermData::collect(get_the_terms($postID, $tax), Collection::class);
            }
        }

        return null;
    }
}
