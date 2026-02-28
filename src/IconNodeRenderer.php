<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark;

use Frostybee\SwarmIcons\Exception\SwarmIconsException;
use Frostybee\SwarmIcons\IconManager;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class IconNodeRenderer implements NodeRendererInterface
{
    /** @var list<string> Attribute names handled via fluent Icon methods instead of SVG attributes */
    private const FLUENT_ATTRIBUTES = ['rotate', 'flip', 'opacity', 'title'];

    public function __construct(
        private readonly IconManager $manager,
        private readonly bool $silentOnMissing = false,
    ) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        IconNode::assertInstanceOf($node);

        /** @var IconNode $node */
        $name = $node->getIconName();
        $attributes = $node->getIconAttributes();

        // Extract attributes that map to fluent Icon methods.
        $transforms = [];
        foreach (self::FLUENT_ATTRIBUTES as $key) {
            if (isset($attributes[$key])) {
                $transforms[$key] = $attributes[$key];
                unset($attributes[$key]);
            }
        }

        try {
            $icon = $this->manager->get($name, $attributes);

            if (isset($transforms['rotate'])) {
                $icon = $icon->rotate((float) $transforms['rotate']);
            }

            if (isset($transforms['flip'])) {
                $icon = $icon->flip($transforms['flip']);
            }

            if (isset($transforms['opacity'])) {
                $icon = $icon->opacity((float) $transforms['opacity']);
            }

            if (isset($transforms['title'])) {
                $icon = $icon->title($transforms['title']);
            }

            return $icon->toHtml();
        } catch (SwarmIconsException $e) {
            if ($this->silentOnMissing) {
                $safeName = str_replace('--', '- -', htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
                $safeError = str_replace('--', '- -', htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));

                return "<!-- SwarmIcons: Icon '{$safeName}' not found ({$safeError}) -->";
            }

            throw $e;
        }
    }
}
