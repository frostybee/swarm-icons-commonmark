<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark;

use League\CommonMark\Node\Inline\AbstractInline;

final class IconNode extends AbstractInline
{
    /**
     * @param string $iconName Icon name (e.g. "tabler:home")
     * @param array<string, string> $attributes Additional attributes
     */
    public function __construct(
        private readonly string $iconName,
        private readonly array $attributes = [],
    ) {
        parent::__construct();
    }

    public function getIconName(): string
    {
        return $this->iconName;
    }

    /**
     * @return array<string, string>
     */
    public function getIconAttributes(): array
    {
        return $this->attributes;
    }
}
