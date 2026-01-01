<?php

// Define the prefixes to check
$prefixes = ['auth', 'messages', 'front', 'panel', 'validation'];

// Base paths
$langPath = __DIR__ . '/resources/lang';

foreach ($prefixes as $prefix) {
    $keysFile = __DIR__ . '/' . $prefix . '.txt';

    // Skip if keys file doesn't exist
    if (!file_exists($keysFile)) {
        echo "$prefix.txt not found, skipping...\n";
        continue;
    }

    // Read keys from file
    $keys = file($keysFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (empty($keys)) {
        echo "$prefix: No keys found\n";
        continue;
    }

    // Load translation files
    $enFile = $langPath . '/en/' . $prefix . '.php';
    $arFile = $langPath . '/ar/' . $prefix . '.php';

    $enTranslations = [];
    $arTranslations = [];

    if (file_exists($enFile)) {
        $enTranslations = include $enFile;
    } else {
        echo "Warning: $enFile not found\n";
    }

    if (file_exists($arFile)) {
        $arTranslations = include $arFile;
    } else {
        echo "Warning: $arFile not found\n";
    }

    $missingEn = [];
    $missingAr = [];

    foreach ($keys as $key) {
        $key = trim($key);
        if (empty($key)) continue;

        // Check if key exists in English
        if (!array_key_exists($key, $enTranslations)) {
            $missingEn[] = $key;
        }

        // Check if key exists in Arabic
        if (!array_key_exists($key, $arTranslations)) {
            $missingAr[] = $key;
        }
    }

    // Create directory for missing translations
    $outputDir = __DIR__ . '/' . $prefix;
    if (!empty($missingEn) || !empty($missingAr)) {
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
    }

    // Write missing English keys
    if (!empty($missingEn)) {
        file_put_contents($outputDir . '/en.txt', implode("\n", $missingEn) . "\n");
    }

    // Write missing Arabic keys
    if (!empty($missingAr)) {
        file_put_contents($outputDir . '/ar.txt', implode("\n", $missingAr) . "\n");
    }

    // Clear the original keys file since all keys have been processed
    file_put_contents($keysFile, '');

    echo "$prefix:\n";
    echo "  - Total keys: " . count($keys) . "\n";
    echo "  - Missing in English: " . count($missingEn) . " keys" . (!empty($missingEn) ? " (saved to $prefix/en.txt)" : "") . "\n";
    echo "  - Missing in Arabic: " . count($missingAr) . " keys" . (!empty($missingAr) ? " (saved to $prefix/ar.txt)" : "") . "\n";
    echo "\n";
}

echo "Done!\n";
