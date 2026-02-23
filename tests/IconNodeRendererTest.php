<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark\Tests;

use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\Exception\IconNotFoundException;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Provider\DirectoryProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

class IconNodeRendererTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/Fixtures/icons';
    }

    public function test_renders_svg_html(): void
    {
        $html = $this->convert(':icon[test:home]');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('</svg>', $html);
    }

    public function test_passes_attributes_to_icon(): void
    {
        $html = $this->convert(':icon[test:home class="w-6 h-6"]');

        $this->assertStringContainsString('w-6 h-6', $html);
    }

    public function test_throws_on_missing_icon_when_not_silent(): void
    {
        $this->expectException(IconNotFoundException::class);

        $this->convert(':icon[test:nonexistent]', silentOnMissing: false);
    }

    public function test_returns_html_comment_when_silent(): void
    {
        $html = $this->convert(':icon[test:nonexistent]', silentOnMissing: true);

        $this->assertStringContainsString('<!-- SwarmIcons:', $html);
        $this->assertStringContainsString('nonexistent', $html);
    }

    public function test_escapes_xss_in_html_comments(): void
    {
        $html = $this->convert(':icon[test:<script>alert(1)</script>]', silentOnMissing: true);

        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_renders_icon_within_paragraph(): void
    {
        $html = $this->convert(':icon[test:home]');

        $this->assertStringContainsString('<p>', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    private function convert(string $markdown, bool $silentOnMissing = true): string
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
