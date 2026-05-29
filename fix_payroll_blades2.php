<?php
$dir = __DIR__ . '/resources/views/payroll';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    $content = str_replace('->employee->basic_salary', '->employee->employee_basic_salary', $content);
    $content = preg_replace('/\$p0\b/', '0', $content);
    $content = preg_replace('/\$payroll0\b/', '0', $content);
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated " . $file->getPathname() . "\n";
    }
}
