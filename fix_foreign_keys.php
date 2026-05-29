<?php
$dir = __DIR__ . '/app/Models';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    // Replace belongsTo(Employee::class) with belongsTo(Employee::class, 'employee_id')
    $content = preg_replace('/belongsTo\(Employee::class\s*\)/', "belongsTo(Employee::class, 'employee_id')", $content);
    $content = preg_replace('/belongsTo\(\\\\?App\\\\Models\\\\Employee::class\s*\)/', "belongsTo(\App\Models\Employee::class, 'employee_id')", $content);
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated " . $file->getPathname() . "\n";
    }
}
echo "Done fixing foreign keys.\n";
