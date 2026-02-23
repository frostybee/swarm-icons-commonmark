<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark\Tests;

use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\CommonMark\IconNode;
use Frostybee\SwarmIcons\CommonMark\IconParser;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Provider\DirectoryProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\MarkdownParser;
use PHPUnit\Framework\TestCase;

class IconParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/Fixtures/icons';
    }

    public function test_match_definition(): void
    {
        $parser = new IconParser();
        $match = $parser->getMatchDefinition();

        $this->assertInstanceOf(InlineParserMatch::class, $match);
    }

    public function test_parses_simple_icon_syntax(): void
    {
        $nodes = $this->parseIconNodes(':icon[test:home]');

        $this->assertCount(1, $nodes);
        $this->assertSame('test:home', $nodes[0]->getIconName());
        $this->assertSame([], $nodes[0]->getIconAttributes());
    }

    public function test_parses_icon_with_class_attribute(): void
    {
        $nodes = $this->parseIconNodes(':icon[test:home class="w-6 h-6"]');

        $this->assertCount(1, $nodes);
        $this->assertSame('test:home', $nodes[0]->getIconName());
        $this->assertSame(['class' => 'w-6 h-6'], $nodes[0]->getIconAttributes());
    }

    public function test_parses_icon_with_multiple_attributes(): void
    {
        $nodes = $this->parseIconNodes(':icon[test:home class="w-6" id="home-icon"]');

        $this->assertCount(1, $nodes);
        $this->assertSame(['class' => 'w-6', 'id' => 'home-icon'], $nodes[0]->getIconAttributes());
    }

    public function test_parses_single_quoted_attributes(): void
    {
        $nodes = $this->parseIconNodes(":icon[test:home class='w-6 h-6']");

        $this->assertCount(1, $nodes);
        $this->assertSame(['class' => 'w-6 h-6'], $nodes[0]->getIconAttributes());
    }

    public function test_empty_brackets_produce_no_icon_nodes(): void
    {
        $nodes = $this->parseIconNodes(':icon[]');

        $this->assertCount(0, $nodes);
    }

    public function test_parses_icon_inline_with_text(): void
    {
        $nodes = $this->parseIconNodes('Click :icon[test:home] to go home');

        $this->assertCount(1, $nodes);
        $this->assertSame('test:home', $nodes[0]->getIconName());
    }

    public function test_parses_multiple_icons(): void
    {
        $nodes = $this->parseIconNodes(':icon[test:home] and :icon[test:user]');

        $this->assertCount(2, $nodes);
        $this->assertSame('test:home', $nodes[0]->getIconName());
        $this->assertSame('test:user', $nodes[1]->getIconName());
    }

    public function test_parses_unquoted_attribute(): void
    {
        $nodes = $this->parseIconNodes(':icon[test:home id=main-icon]');

        $this->assertCount(1, $nodes);
        $this->assertSame(['id' => 'main-icon'], $nodes[0]->getIconAttributes());
    }

    /**
     * Parse markdown and return all IconNode instances from the AST.
     *
     * @return array<IconNode>
     */
    private function parseIconNodes(string $markdown): array
    {
        $manager = new IconManager();
        $manager->register('test', new DirectoryProvider($this->fixturesPath));

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new IconExtension($manager, silentOnMissing: true));

        $parser = new MarkdownParser($environment);
        $document = $parser->parse($markdown);

        $nodes = [];
        foreach ($document->iterator() as $node) {
            if ($node instanceof IconNode) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }
}
