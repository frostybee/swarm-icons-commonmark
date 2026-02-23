<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark\Tests;

use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Provider\DirectoryProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

class IconExtensionTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/Fixtures/icons';
    }

    public function test_registers_parser_and_renderer(): void
    {
        // Verify registration works by converting markdown with an icon.
        // If the parser or renderer weren't registered, this would output
        // the raw :icon[] syntax instead of SVG markup.
        $html = $this->convert(':icon[test:home]');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringNotContainsString(':icon[', $html);
    }

    public function test_full_pipeline_renders_icon(): void
    {
        $html = $this->convert(':icon[test:home]');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('</svg>', $html);
    }

    public function test_silent_mode_propagates_to_renderer(): void
    {
        $html = $this->convert(':icon[test:nonexistent]', silentOnMissing: true);

        $this->assertStringContainsString('<!-- SwarmIcons:', $html);
    }

    public function test_icon_in_heading(): void
    {
        $html = $this->convert('# :icon[test:home] Home');

        $this->assertStringContainsString('<h1>', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    private function convert(string $markdown, bool $silentOnMissing = false): string
    {
        $manager = new IconManager();
        $manager->register('test', new DirectoryProvider($this->fixturesPath));

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new IconExtension($manager, $silentOnMissing));

        $converter = new MarkdownConverter($environment);

        return $converter->convert($markdown)->getContent();
    }
}
