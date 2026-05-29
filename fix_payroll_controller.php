<?php
$file = __DIR__ . '/app/Http/Controllers/PayrollController.php';
$content = file_get_contents($file);

// Index method (employee)
$content = str_replace("->where('status', 'approved')", "->where('payroll_status', 'approved')", $content);
$content = str_replace("->avg('net_salary')", "->avg('payroll_net_salary')", $content);
$content = str_replace("->where('year', \$parts[0])->where('month', \$parts[1])", "->where('payroll_periode_year', \$parts[0])->where('payroll_periode_month', \$parts[1])", $content);
$content = str_replace("\$currentGross = \$latest->employee_basic_salary + \$latest->allowances;", "\$currentGross = (\$latest->employee->basic_salary ?? 0) + (\$latest->transactional ? \$latest->transactional->transactional_total : 0);", $content);
$content = str_replace("\$currentNet = \$latest->net_salary;", "\$currentNet = \$latest->payroll_net_salary;", $content);
$content = str_replace("->select('year', 'month')", "->select('payroll_periode_year as year', 'payroll_periode_month as month')", $content);
$content = str_replace("->orderBy('year', 'desc')", "->orderBy('payroll_periode_year', 'desc')", $content);
$content = str_replace("->orderByRaw(\"FIELD(month,", "->orderByRaw(\"FIELD(payroll_periode_month,", $content);

// Index method (owner)
$content = str_replace("->select('month', 'year')", "->select('payroll_periode_month as month', 'payroll_periode_year as year')", $content);
$content = str_replace("->selectRaw('count(DISTINCT user_id) as total_employees')", "->selectRaw('count(DISTINCT employee_id) as total_employees')", $content);
$content = str_replace("->selectRaw('sum(net_salary) as total_salary')", "->selectRaw('sum(payroll_net_salary) as total_salary')", $content);
$content = str_replace("->groupBy('month', 'year')", "->groupBy('payroll_periode_month', 'payroll_periode_year')", $content);

// generatePDF & Report methods
$content = str_replace("->where('month', \$month)", "->where('payroll_periode_month', \$month)", $content);
$content = str_replace("->where('year', \$year)", "->where('payroll_periode_year', \$year)", $content);
$content = str_replace("return \$p->employee_basic_salary + \$p->allowances;", "return (\$p->employee->basic_salary ?? 0) + (\$p->transactional ? \$p->transactional->transactional_total : 0);", $content);
$content = str_replace("return \$p->deductions + \$p->potongan_alpha + \$p->denda_terlambat;", "return \$p->payroll_tax + \$p->payroll_total_late;", $content);
$content = str_replace("->sum('net_salary')", "->sum('payroll_net_salary')", $content);
$content = str_replace("->where('status', 'pending')", "->where('payroll_status', 'pending')", $content);
$content = str_replace("->where('status', 'rejected')", "->where('payroll_status', 'rejected')", $content);

$content = str_replace("return \$p->potongan_alpha + \$p->denda_terlambat;", "return \$p->payroll_total_late;", $content);
$content = str_replace("->sum('deductions')", "->sum('payroll_tax')", $content);
$content = str_replace("\$totalBpjsDeduction = 0;", "\$totalBpjsDeduction = \$payrolls->sum(function (\$p) { return \$p->transactional ? \$p->transactional->transactional_bpjs : 0; });", $content);

// approveBatch
$content = str_replace("'exists:payrolls,id'", "'exists:payrolls,payroll_id'", $content);
$content = str_replace("whereIn('id', \$request->payroll_ids)->update(['status' => \$status]);", "whereIn('payroll_id', \$request->payroll_ids)->update(['payroll_status' => \$status]);", $content);

file_put_contents($file, $content);
echo "PayrollController updated!\n";
