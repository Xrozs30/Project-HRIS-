<?php
$directories = [__DIR__ . '/routes'];

$replacements = [
    'App\Models\User' => 'App\Models\Employee',
    "'user_id'" => "'employee_id'",
    '"user_id"' => '"employee_id"',
    '->user_id' => '->employee_id',
    '->user()' => '->employee()',
    '->name' => '->employee_name',
    "['name']" => "['employee_name']",
    "['name'" => "['employee_name'",
    '->email' => '->employee_email',
    "['email']" => "['employee_email']",
    '->nik' => '->employee_nik',
    "['nik']" => "['employee_nik']",
    '->phone' => '->employee_phone',
    '->address' => '->employee_addres',
    '->position' => '->position_type',
    "['position']" => "['position_type']",
    '->pph21_status' => '->tax_status',
    '->basic_salary' => '->employee_basic_salary',
    '->password' => '->employee_password',
    '->face_descriptor' => '->employee_face_descriptor',
    '->account_number' => '->employee_bank_number',
    '->bank_name' => '->employee_bank_name',
    '->bpjs_number' => '->employee_bpjs_number',
    '->gender' => '->employee_gender',
    '->birth_date' => '->employee_birth_date',
    '->id' => '->employee_id', // RISKY: might replace other IDs, we will use regex for user objects
];

function processDir($dir, $replacements) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        foreach ($replacements as $search => $replace) {
            if ($search === '->id') continue; // Handle separately
            $content = str_replace($search, $replace, $content);
        }
        
        // Handle ->id for user specific cases
        $content = preg_replace('/(\$user|\$employee|Auth::user\(\)|auth\(\)->user\(\))->id/', '$1->employee_id', $content);
        
        // Handle $user variable rename to $employee where appropriate
        // (Leaving $user is okay, but replacing is better for consistency. But we will just replace the properties for safety)
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}

foreach ($directories as $dir) {
    processDir($dir, $replacements);
}
echo "Done replacing.\n";
