<?php
/**
 * Build Script - Minifies assets for production
 * 
 * Run with: php build.php
 * This will create minified versions of CSS and JS files
 */

define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/config/asset_minifier.php';

$startTime = microtime(true);

echo "=== IMS Asset Minification ===\n\n";

$files = [
    'css' => [
        'public/css/layout.css' => 'public/css/layout.min.css',
        'public/css/dashboard.css' => 'public/css/dashboard.min.css',
        'public/css/login.css' => 'public/css/login.min.css'
    ],
    'js' => [
        'public/js/layout.js' => 'public/js/layout.min.js',
        'public/js/dashboard.js' => 'public/js/dashboard.min.js',
        'public/js/script.js' => 'public/js/script.min.js'
    ]
];

$totalSaved = 0;

// Minify CSS files
echo "[CSS Minification]\n";
foreach ($files['css'] as $input => $output) {
    if (file_exists($input)) {
        $originalSize = filesize($input);
        if (AssetMinifier::minifyCSSFile($input, $output)) {
            $minifiedSize = filesize($output);
            $info = AssetMinifier::getSizeReduction($originalSize, $minifiedSize);
            echo "✓ " . basename($input) . " → " . basename($output) . "\n";
            echo "  Size: " . round($originalSize/1024, 2) . "KB → " . round($minifiedSize/1024, 2) . "KB ";
            echo "(" . $info['percentage'] . "% reduction)\n\n";
            $totalSaved += $info['saved'];
        }
    }
}

// Minify JS files
echo "[JavaScript Minification]\n";
foreach ($files['js'] as $input => $output) {
    if (file_exists($input)) {
        $originalSize = filesize($input);
        if (AssetMinifier::minifyJSFile($input, $output)) {
            $minifiedSize = filesize($output);
            $info = AssetMinifier::getSizeReduction($originalSize, $minifiedSize);
            echo "✓ " . basename($input) . " → " . basename($output) . "\n";
            echo "  Size: " . round($originalSize/1024, 2) . "KB → " . round($minifiedSize/1024, 2) . "KB ";
            echo "(" . $info['percentage'] . "% reduction)\n\n";
            $totalSaved += $info['saved'];
        }
    }
}

$endTime = microtime(true);
$duration = round(($endTime - $startTime) * 1000, 2);

echo "=== Build Complete ===\n";
echo "Total Space Saved: " . round($totalSaved/1024, 2) . "KB\n";
echo "Build Time: " . $duration . "ms\n\n";

echo "Next: Update layout.php to load minified versions in production\n";
echo "Change: <link href=\"css/layout.css\"> to <link href=\"css/layout.min.css\">\n";
?>
