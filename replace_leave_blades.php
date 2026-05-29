<?php

$directories = [
    __DIR__ . '/resources/views/leave',
    __DIR__ . '/resources/views/hr/leave'
];

function processDir($dir) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Replace properties
        $content = str_replace('->type', '->leave_type', $content);
        $content = str_replace('->start_date', '->leave_start_date', $content);
        $content = str_replace('->end_date', '->leave_end_date', $content);
        $content = str_replace('->duration', '->leave_duration', $content);
        $content = str_replace('->reason', '->leave_reason', $content);
        $content = str_replace('->status', '->leave_status', $content);
        $content = str_replace('->proof_file_path', '->leave_sick_proof', $content);
        $content = str_replace('->rejection_reason', '->leave_rejection_reason', $content);
        
        // IDs
        $content = str_replace('$req->id', '$req->leave_id', $content);
        $content = str_replace('openRejectModal({{ $req->id }})', 'openRejectModal(\'{{ $req->leave_id }}\')', $content);
        
        // Form names
        $content = str_replace('name="type"', 'name="leave_type"', $content);
        $content = str_replace('name="start_date"', 'name="leave_start_date"', $content);
        $content = str_replace('name="end_date"', 'name="leave_end_date"', $content);
        $content = str_replace('name="reason"', 'name="leave_reason"', $content);
        $content = str_replace('name="proof_file"', 'name="leave_sick_proof"', $content);
        $content = str_replace('name="rejection_reason"', 'name="leave_rejection_reason"', $content);
        
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
