<?php

$directories = [
    __DIR__ . '/resources/views/presence',
    __DIR__ . '/resources/views/dashboard',
    __DIR__ . '/resources/views/layouts',
    __DIR__ . '/resources/views'
];

function processDir($dir) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Routes and URLs
        $content = str_replace('route("presensi.', 'route("presence.', $content);
        $content = str_replace("route('presensi.", "route('presence.", $content);
        $content = str_replace("url('/presensi')", "url('/presence')", $content);
        $content = str_replace("is('presensi*')", "is('presence*')", $content);
        $content = str_replace("routeIs('presensi.", "routeIs('presence.", $content);
        
        // Form names/columns
        $content = str_replace('name="lat"', 'name="presence_lat"', $content);
        $content = str_replace('name="long"', 'name="presence_long"', $content);
        
        // Object properties
        $content = str_replace('->date', '->presence_date', $content);
        $content = str_replace('->time_in', '->presence_time_in', $content);
        $content = str_replace('->time_out', '->presence_time_out', $content);
        $content = str_replace('->photo_in', '->presence_photo_in', $content);
        $content = str_replace('->photo_out', '->presence_photo_out', $content);
        $content = str_replace('->lat', '->presence_lat', $content);
        $content = str_replace('->long', '->presence_long', $content);
        $content = str_replace('->status', '->presence_status', $content);
        $content = str_replace("storage/presensi/", "storage/presence/", $content);
        
        // Relationship
        $content = str_replace('->employee->name', '->employee_name', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}

foreach ($directories as $dir) {
    processDir($dir);
}
echo "Done replacing blade variables.\n";
