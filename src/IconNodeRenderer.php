<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark;

use Frostybee\SwarmIcons\Exception\IconNotFoundException;
use Frostybee\SwarmIcons\Exception\InvalidIconNameException;
use Frostybee\SwarmIcons\IconManager;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class IconNodeRenderer implements NodeRendererInterface
{
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

        try {
            return $this->manager->get($name, $attributes)->toHtml();
        } catch (IconNotFoundException | InvalidIconNameException $e) {
            if ($this->silentOnMissing) {
                $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                $safeError = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');

                return "<!-- SwarmIcons: Icon '{$safeName}' not found ({$safeError}) -->";
            }

            throw $e;
        }
    }
}
