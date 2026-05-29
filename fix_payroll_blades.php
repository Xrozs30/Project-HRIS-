<?php
$dir = __DIR__ . '/resources/views/payroll';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    
    $content = file_get_contents($file);
    $originalContent = $content;
    
    // Exact property replacements
    $content = preg_replace('/->net_salary\b/', '->payroll_net_salary', $content);
    $content = preg_replace('/->status\b/', '->payroll_status', $content);
    
    // For month and year, it's tricky because $group->month is valid. We know the loop variables:
    // In report: $p->month
    $content = preg_replace('/\$p->month\b/', '$p->payroll_periode_month', $content);
    $content = preg_replace('/\$p->year\b/', '$p->payroll_periode_year', $content);
    $content = preg_replace('/\$payroll->month\b/', '$payroll->payroll_periode_month', $content);
    $content = preg_replace('/\$payroll->year\b/', '$payroll->payroll_periode_year', $content);
    
    // Model replacements
    $content = preg_replace('/->employee_basic_salary\b/', '->employee->basic_salary', $content);
    $content = preg_replace('/->allowances\b/', '->transactional->transactional_total', $content);
    $content = preg_replace('/->deductions\b/', '->payroll_tax', $content);
    $content = preg_replace('/->denda_terlambat\b/', '->payroll_total_late', $content);
    $content = preg_replace('/->potongan_alpha\b/', '0', $content); // Alpha is removed
    $content = preg_replace('/->id\b/', '->payroll_id', $content); // In checkboxes value="{{ $p->id }}" -> payroll_id
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "Updated " . $file->getPathname() . "\n";
    }
}
