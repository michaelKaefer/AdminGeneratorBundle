<?php

namespace MichaelKaefer\AdminUiBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class AsMenuItem
{
    public function __construct(
        private(set) string $menu,
        private(set) ?string $child = null,
        private(set) ?int $priority = 0,
    ) {
    }
}
