<?php
$directories = [
    __DIR__ . '/app/Http/Controllers',
    __DIR__ . '/routes',
    __DIR__ . '/resources/views'
];

function processDir($dir) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        $content = str_replace('auth()->employee()', 'auth()->user()', $content);
        $content = str_replace('Auth::employee()', 'Auth::user()', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}

foreach ($directories as $dir) {
    processDir($dir);
}
echo "Done replacing.\n";
