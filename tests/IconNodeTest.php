<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark\Tests;

use Frostybee\SwarmIcons\CommonMark\IconNode;
use PHPUnit\Framework\TestCase;

class IconNodeTest extends TestCase
{
    public function test_stores_icon_name(): void
    {
        $node = new IconNode('tabler:home');

        $this->assertSame('tabler:home', $node->getIconName());
    }

    public function test_default_attributes_are_empty(): void
    {
        $node = new IconNode('tabler:home');

        $this->assertSame([], $node->getIconAttributes());
    }

    public function test_stores_attributes(): void
    {
        $attributes = ['class' => 'w-6 h-6', 'id' => 'icon-home'];
        $node = new IconNode('tabler:home', $attributes);

        $this->assertSame($attributes, $node->getIconAttributes());
    }
}
