<?php

// Define the prefixes to search for
$prefixes = ['auth', 'messages', 'front', 'panel', 'validation'];

// Initialize arrays to store keys for each prefix
$keys = [];
foreach ($prefixes as $prefix) {
    $keys[$prefix] = [];
}

// Function to recursively get all PHP files
function getPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

// Function to extract keys from file content
function extractKeys($content, $prefixes) {
    $foundKeys = [];

    foreach ($prefixes as $prefix) {
        // Pattern to match __('prefix.key') or __("prefix.key")
        // Also matches trans('prefix.key') and @lang('prefix.key')
        $pattern = '/__\s*\(\s*[\'"]' . preg_quote($prefix, '/') . '\.([^\'"]+)[\'"]\s*\)/';

        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $foundKeys[$prefix][] = $key;
            }
        }

        // Also check for trans() function
        $pattern2 = '/trans\s*\(\s*[\'"]' . preg_quote($prefix, '/') . '\.([^\'"]+)[\'"]\s*\)/';

        if (preg_match_all($pattern2, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $foundKeys[$prefix][] = $key;
            }
        }

        // Also check for __() shorthand with variable
        $pattern3 = '/__(\'[^\']*' . preg_quote($prefix, '/') . '\.[^\'"]*[\'"]|"[^"]*' . preg_quote($prefix, '/') . '\.[^"]*")/';

        if (preg_match_all($pattern3, $content, $matches)) {
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

// Get all PHP files in app directory
$appDir = __DIR__ . '/app';
$files = getPhpFiles($appDir);

echo "Scanning " . count($files) . " PHP files in app/ directory (recursive)...\n\n";

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

// Write results to files with _app suffix
foreach ($prefixes as $prefix) {
    $filename = $prefix . '_app.txt';
    $content = implode("\n", $keys[$prefix]);

    if (!empty($keys[$prefix])) {
        $content .= "\n";
    }

    file_put_contents($filename, $content);
    echo "$prefix: " . count($keys[$prefix]) . " unique keys (saved to $filename)\n";
}

echo "\nDone!\n";
