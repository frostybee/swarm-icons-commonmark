# swarm-icons-commonmark

CommonMark extension for the [frostybee/swarm-icons](https://github.com/swarm-icons/swarm-icons) PHP library. Renders inline SVG icons in markdown using the `:icon[prefix:name]` syntax — no JavaScript, no font files, just SVG markup in your HTML output.

## Installation

```bash
composer require frostybee/swarm-icons-commonmark
```

This will pull in `frostybee/swarm-icons` and `league/commonmark` automatically.

## Usage

### With Local SVG Files

Register a `DirectoryProvider` pointing to a local directory of SVG files:

```php
use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Provider\DirectoryProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$manager = new IconManager();
$manager->register('tabler', new DirectoryProvider('/path/to/tabler/svgs'));

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new IconExtension($manager));

$converter = new MarkdownConverter($environment);
```

### With the Iconify API

Use `IconifyProvider` to fetch icons on demand from the [Iconify API](https://iconify.design/) (200,000+ icons, no downloads required):

```php
use Frostybee\SwarmIcons\CommonMark\IconExtension;
use Frostybee\SwarmIcons\IconManager;
use Frostybee\SwarmIcons\Cache\FileCache;
use Frostybee\SwarmIcons\Provider\IconifyProvider;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$manager = new IconManager();
$cache = new FileCache('/path/to/cache');
$manager->register('tabler', new IconifyProvider('tabler', $cache));

$environment = new Environment();
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new IconExtension($manager));

$converter = new MarkdownConverter($environment);
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
