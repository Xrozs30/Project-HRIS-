<?php

$directories = [
    __DIR__ . '/resources/views/overtime',
    __DIR__ . '/resources/views/hr/overtime'
];

function processDir($dir) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Remove assigned_by block
        $content = preg_replace('/<div class="mb-5">\s*<label for="assigned_by".*?<\/div>\s*<\/div>/s', '', $content);

        // Form names
        $content = str_replace('name="date"', 'name="overtime_date"', $content);
        $content = str_replace('name="start_time"', 'name="overtime_start"', $content);
        $content = str_replace('name="end_time"', 'name="overtime_finish"', $content);
        $content = str_replace('name="description"', 'name="overtime_description"', $content);
        
        // Properties
        $content = str_replace('->date', '->overtime_date', $content);
        $content = str_replace('->start_time', '->overtime_start', $content);
        $content = str_replace('->end_time', '->overtime_finish', $content);
        $content = str_replace('->status', '->overtime_status', $content);
        $content = str_replace('->description', '->overtime_description', $content);
        $content = str_replace('->created_at', '->overtime_create_at', $content);
        
        // Relationship
        $content = str_replace('->user->name', '->employee_name', $content);
        $content = str_replace('->user->employee_nik', '->employee->employee_nik', $content);
        $content = str_replace('->user->position_type', '->employee->position_type', $content);
        
        // IDs
        $content = str_replace('$ot->id', '$ot->overtime_id', $content);
        $content = str_replace('approveModal({{ $ot->id }})', 'approveModal(\'{{ $ot->overtime_id }}\')', $content);
        $content = str_replace('rejectModal({{ $ot->id }})', 'rejectModal(\'{{ $ot->overtime_id }}\')', $content);
        $content = str_replace('approveModal(\'{{ $ot->id }}\')', 'approveModal(\'{{ $ot->overtime_id }}\')', $content);
        $content = str_replace('rejectModal(\'{{ $ot->id }}\')', 'rejectModal(\'{{ $ot->overtime_id }}\')', $content);
        
        // Display duration
        // We will manually inject the duration column in the tables. 

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
