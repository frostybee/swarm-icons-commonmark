<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Cache\FileCache;
use Frostybee\SwarmIcons\Provider\IconifyProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

// --- Setup ---
$manager = new IconManager();
$cache = new FileCache(__DIR__ . '/../cache');
$manager->register('tabler', new IconifyProvider('tabler', $cache));

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new IconExtension($manager, silentOnMissing: true));

$converter = new MarkdownConverter($environment);

// --- Markdown samples ---
$markdown = <<<'MD'
## :icon[tabler:home] Basic Usage

A simple inline icon: :icon[tabler:home] appears right in the text.

## :icon[tabler:palette] With Attributes

An icon with a CSS class: :icon[tabler:user class="icon-lg text-purple"]

An icon with multiple attributes: :icon[tabler:settings class="icon-spin text-orange" id="settings-icon"]

## :icon[tabler:list] Icons in Lists

- :icon[tabler:mail class="text-blue"] Email notifications
- :icon[tabler:bell class="text-amber"] Push notifications
- :icon[tabler:message-circle class="text-green"] Chat messages

## :icon[tabler:layout-grid] Multiple Icons

:icon[tabler:brand-github class="text-gray"] GitHub · :icon[tabler:brand-twitter class="text-sky"] Twitter · :icon[tabler:brand-linkedin class="text-indigo"] LinkedIn

## :icon[tabler:alert-triangle class="text-red"] Missing Icon (Silent Mode)

This icon does not exist: :icon[tabler:nonexistent-icon-xyz] - silent mode renders an HTML comment instead of throwing.
MD;

// --- Render ---
$html = $converter->convert($markdown)->getContent();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swarm Icons CommonMark Demo</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; color: #1a1a1a; }
        h1 { border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
        .section { margin-bottom: 2rem; }
        .section-title { font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; }
        pre { background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; font-size: 0.875rem; }
        .rendered { background: #fff; border: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 0.5rem; }
        .rendered svg { width: 1.25em; height: 1.25em; vertical-align: middle; display: inline-block; }
        .rendered .icon-lg { width: 2em; height: 2em; }
        .rendered .text-blue { color: #2563eb; }
        .rendered .text-amber { color: #d97706; }
        .rendered .text-green { color: #16a34a; }
        .rendered .text-red { color: #dc2626; }
        .rendered .text-purple { color: #9333ea; }
        .rendered .text-orange { color: #ea580c; }
        .rendered .text-sky { color: #0284c7; }
        .rendered .text-indigo { color: #4f46e5; }
        .rendered .text-gray { color: #4b5563; }
    </style>
</head>
<body>
    <h1>Swarm Icons CommonMark — Demo</h1>

    <div class="section">
        <div class="section-title">Markdown Source</div>
        <pre><code><?= htmlspecialchars($markdown) ?></code></pre>
    </div>

    <div class="section">
        <div class="section-title">Rendered Output</div>
        <div class="rendered">
            <?= $html ?>
        </div>
    </div>
</body>
</html>
