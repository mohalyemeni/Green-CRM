<?php

chdir(__DIR__);
$base = __DIR__ . '/resources/views/pages/leads/';

$maps = [
    'create' => '⚡create',
    'edit'   => '⚡edit',
    'index'  => '⚡index',
    'show'   => '⚡show',
];

foreach ($maps as $file => $folder) {
    $destDir = $base . $folder;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0777, true);
        echo "Created dir: $folder\n";
    }
    if (file_exists($base . $file . '.php')) {
        rename($base . $file . '.php', $destDir . '/index.php');
        echo "Moved $file.php -> $folder/index.php\n";
    }
    if (file_exists($base . $file . '.blade.php')) {
        rename($base . $file . '.blade.php', $destDir . '/index.blade.php');
        echo "Moved $file.blade.php -> $folder/index.blade.php\n";
    }
}

// Fix render view path in index.php
$indexPhp = $base . '⚡index/index.php';
if (file_exists($indexPhp)) {
    $c = file_get_contents($indexPhp);
    $c = str_replace("'pages.leads.index'", "'pages.leads.⚡index.index'", $c);
    file_put_contents($indexPhp, $c);
    echo "Fixed view path in ⚡index/index.php\n";
}

echo "Done!\n";
