# swarm-icons-commonmark

CommonMark extension for the [frostybee/swarm-icons](https://github.com/swarm-icons/swarm-icons) PHP library. Renders inline SVG icons in markdown using the `:icon[prefix:name]` syntax — no JavaScript, no font files, just SVG markup in your HTML output.

## Installation

```bash
composer require frostybee/swarm-icons-commonmark
```

This will pull in `frostybee/swarm-icons` and `league/commonmark` automatically.

## Usage

### With JSON Collections (Recommended)

Download icon sets via the Swarm Icons CLI, then use `SwarmIconsConfig` to auto-discover them:

```bash
php vendor/bin/swarm-icons json:download tabler heroicons mdi
```

```php
use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\SwarmIconsConfig;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$manager = SwarmIconsConfig::create()
    ->discoverJsonSets()
    ->cachePath('/path/to/cache')
    ->build();

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new IconExtension($manager));

$converter = new MarkdownConverter($environment);
```

### With Local SVG Files

Register a local directory of SVG files:

```php
$manager = SwarmIconsConfig::create()
    ->addDirectory('custom', '/path/to/svgs')
    ->build();
```

### With the Iconify API

Fetch icons on demand from the [Iconify API](https://iconify.design/) (200,000+ icons, no downloads required):

```php
$manager = SwarmIconsConfig::create()
    ->addIconifySet('heroicons')
    ->cachePath('/path/to/cache')
    ->build();
```

Any [Iconify prefix](https://icon-sets.iconify.design/) works: `tabler`, `heroicons`, `lucide`, `mdi`, etc.

Then use the `:icon[]` syntax in your markdown:

```markdown
Click :icon[tabler:home] to go home.

# :icon[tabler:settings] Settings

:icon[tabler:user class="w-6 h-6"] Profile
```

### Attributes

Pass HTML attributes directly in the syntax:

```markdown
:icon[tabler:home class="w-6 h-6"]
:icon[tabler:home class="text-blue-500" id="home-icon"]
```

### Transforms

Rotate, flip, and adjust opacity using built-in attributes that map to the `Icon` fluent API:

```markdown
:icon[tabler:arrow-up rotate="90"]
:icon[tabler:arrow-right flip="horizontal"]
:icon[tabler:star opacity="0.5"]
:icon[tabler:info-circle title="More info"]
```

Combine transforms with regular attributes:

```markdown
:icon[tabler:home class="w-6 text-blue-500" rotate="180" opacity="0.8" title="Home"]
```

| Attribute | Description                      | Values                                   |
| --------- | -------------------------------- | ---------------------------------------- |
| `rotate`  | Rotate the icon                  | Degrees (e.g., `"90"`, `"180"`, `"270"`) |
| `flip`    | Flip the icon                    | `"horizontal"`, `"vertical"`, `"both"`   |
| `opacity` | Set icon opacity                 | `"0.0"` to `"1.0"` (e.g., `"0.5"`)      |
| `title`   | Add accessible `<title>` element | Any text (e.g., `"Home icon"`)           |

### Silent Mode

By default, missing icons throw an exception. Enable silent mode to render an HTML comment instead:

```php
$environment->addExtension(new IconExtension($manager, silentOnMissing: true));
```

## Development

Install dev dependencies:

```bash
composer install
```

### Available Commands

| Command              | Description                          |
| -------------------- | ------------------------------------ |
| `composer test`      | Run PHPUnit tests                    |
| `composer phpstan`   | Run PHPStan static analysis (level 8)|
| `composer cs-check`  | Check code style (dry-run)           |
| `composer cs-fix`    | Auto-fix code style                  |
| `composer test-all`  | Run PHPStan, CS check, and tests     |

## License

MIT
