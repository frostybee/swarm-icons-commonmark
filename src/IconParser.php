<?php

declare(strict_types=1);

namespace Frostybee\SwarmIcons\CommonMark;

use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;

final class IconParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex(':icon\[([^\]]*)\]');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $matches = $inlineContext->getMatches();
        $content = trim($matches[1] ?? '');

        if ($content === '') {
            return false;
        }

        // Advance cursor past the full match only after validation
        $cursor = $inlineContext->getCursor();
        $cursor->advanceBy($inlineContext->getFullMatchLength());

        // Split on first space: icon name + optional attribute string
        $parts = preg_split('/\s+/', $content, 2);

        if ($parts === false) {
            return false;
        }

        $iconName = $parts[0];
        $attributeString = $parts[1] ?? '';

        $attributes = $this->parseAttributes($attributeString);

        $inlineContext->getContainer()->appendChild(
            new IconNode($iconName, $attributes),
        );

        return true;
    }

    /**
     * Parse attribute string into key-value pairs.
     *
     * Supports: key="val", key='val', key=val
     *
     * @return array<string, string>
     */
    private function parseAttributes(string $attributeString): array
    {
        if ($attributeString === '') {
            return [];
        }

        $attributes = [];

        // Match key="value", key='value', or key=value patterns
        preg_match_all(
            '/(\w[\w-]*)=(?:"([^"]*)"|\'([^\']*)\'|(\S+))/',
            $attributeString,
            $matches,
            PREG_SET_ORDER,
        );

        foreach ($matches as $match) {
            $key = $match[1];
            // Use the first non-empty capture group (double-quoted, single-quoted, or unquoted)
            $value = ($match[2] ?? '') !== '' ? $match[2] : (($match[3] ?? '') !== '' ? $match[3] : ($match[4] ?? ''));
            $attributes[$key] = $value;
        }

        return $attributes;
    }
}
