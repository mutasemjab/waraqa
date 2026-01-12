<?php

// Define the prefixes to search for
$prefixes = ['auth', 'messages', 'front', 'panel', 'validation'];

// Initialize arrays to store keys for each prefix
$keys = [];
foreach ($prefixes as $prefix) {
    $keys[$prefix] = [];
}

// Function to recursively get all PHP/Blade files
function getPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && ($file->getExtension() === 'php' || $file->getExtension() === 'blade')) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

// Function to extract keys from file content
function extractKeys($content, $prefixes) {
    $foundKeys = [];

    foreach ($prefixes as $prefix) {
        // Pattern to match __('prefix.key') or __(\"prefix.key\")
        // Also matches trans('prefix.key') and @lang('prefix.key')
        $pattern = '/__\s*\(\s*[\'"]' . preg_quote($prefix, '/') . '\.([^\'"]+)[\'\"]\s*\)/';;

        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $foundKeys[$prefix][] = $key;
            }
        }

        // Also check for trans() function
        $pattern2 = '/trans\s*\(\s*[\'"]' . preg_quote($prefix, '/') . '\.([^\'"]+)[\'\"]\s*\)/';;

        if (preg_match_all($pattern2, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $foundKeys[$prefix][] = $key;
            }
        }

        // Also check for @lang() blade directive
        $pattern3 = '/@lang\s*\(\s*[\'"]' . preg_quote($prefix, '/') . '\.([^\'"]+)[\'\"]\s*\)/';;

        if (preg_match_all($pattern3, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $foundKeys[$prefix][] = $key;
            }
        }

        // Also check for __() shorthand with variable
        $pattern4 = '/__(\s*[\'"][^\'"]*' . preg_quote($prefix, '/') . '\.[^\'"]*[\'"]|"[^"]*' . preg_quote($prefix, '/') . '\.[^"]*")/';;

        if (preg_match_all($pattern4, $content, $matches)) {
            foreach ($matches[1] as $match) {
                // Extract the key from the match
                preg_match('/' . preg_quote($prefix, '/') . '\.([^\'"]+)/', $match, $keyMatch);
                if (isset($keyMatch[1])) {
                    $foundKeys[$prefix][] = $keyMatch[1];
                }
            }
        }
    }

    return $foundKeys;
}

// Get all PHP/Blade files in resources/views directory
$viewsDir = __DIR__ . '/resources/views';
$files = getPhpFiles($viewsDir);

echo "Scanning " . count($files) . " PHP/Blade files in resources/views/ directory (recursive)...\n\n";

// Process each file
foreach ($files as $file) {
    $content = file_get_contents($file);
    $foundKeys = extractKeys($content, $prefixes);

    foreach ($foundKeys as $prefix => $prefixKeys) {
        foreach ($prefixKeys as $key) {
            $keys[$prefix][] = $key;
        }
    }
}

// Remove duplicates and sort
foreach ($prefixes as $prefix) {
    $keys[$prefix] = array_unique($keys[$prefix]);
    sort($keys[$prefix]);
}

// Write results to files (without _app suffix for use with check_all_translations.php)
foreach ($prefixes as $prefix) {
    $filename = $prefix . '.txt';
    $content = implode("\n", $keys[$prefix]);

    if (!empty($keys[$prefix])) {
        $content .= "\n";
    }

    file_put_contents($filename, $content);
    echo "$prefix: " . count($keys[$prefix]) . " unique keys (saved to $filename)\n";
}

echo "\nDone!\n";
