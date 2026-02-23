<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark;

use Frostybee\SwarmIcons\IconManager;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class IconExtension implements ExtensionInterface
{
    public function __construct(
        private readonly IconManager $manager,
        private readonly bool $silentOnMissing = false,
    ) {}

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addInlineParser(new IconParser());
        $environment->addRenderer(IconNode::class, new IconNodeRenderer($this->manager, $this->silentOnMissing));
    }
}
