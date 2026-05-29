<?php

$directories = [
    __DIR__ . '/resources/views/reimbursement',
    __DIR__ . '/resources/views/hr/reimbursement',
    __DIR__ . '/resources/views/payroll',
];

function processDir($dir) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Form names/columns
        $content = str_replace('name="date"', 'name="reimburse_date"', $content);
        $content = str_replace('name="amount"', 'name="reimburse_total"', $content);
        $content = str_replace('name="description"', 'name="reimburse_description"', $content);
        $content = str_replace('name="file_path"', 'name="reimburse_proof"', $content);
        
        // Object properties
        $content = preg_replace('/->date\b/', '->reimburse_date', $content);
        $content = preg_replace('/->amount\b/', '->reimburse_total', $content);
        $content = preg_replace('/->description\b/', '->reimburse_description', $content);
        $content = preg_replace('/->file_path\b/', '->reimburse_proof', $content);
        $content = preg_replace('/->hr_notes\b/', '->reimburse_notes', $content);
        $content = preg_replace('/->status\b/', '->reimburse_status', $content);
        
        // Relationship
        $content = preg_replace('/->user->name\b/', '->employee_name', $content);
        $content = preg_replace('/->employee->name\b/', '->employee_name', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}

foreach ($directories as $dir) {
    processDir($dir);
}
echo "Done replacing blade variables for reimbursement.\n";
