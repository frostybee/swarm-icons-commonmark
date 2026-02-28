<?php

declare(strict_types=1);

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache cleared. <a href="index.php">Back to demo</a>';
} else {
    echo 'OPcache is not available.';
}
