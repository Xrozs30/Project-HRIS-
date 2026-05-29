<?php
$models = ['Absence', 'LeaveRequest', 'Overtime', 'Payroll', 'Presensi', 'Reimbursement'];

foreach ($models as $model) {
    $path = __DIR__ . '/app/Models/' . $model . '.php';
    if (!file_exists($path)) {
        echo "Not found: $path\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    // Replace User::class with Employee::class
    $content = str_replace('User::class', 'Employee::class', $content);
    
    // Replace user() with employee()
    $content = preg_replace('/public function user\(\)/', 'public function employee()', $content);
    
    // Replace user_id with employee_id in fillable
    $content = str_replace("'user_id'", "'employee_id'", $content);
    $content = str_replace('"user_id"', '"employee_id"', $content);
    
    file_put_contents($path, $content);
    echo "Updated $model\n";
}
