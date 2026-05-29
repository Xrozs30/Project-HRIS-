<!DOCTYPE html>
<html>
<head>
    <title>Payroll Report {{ $month }} {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #f8f9fa; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { width: 48%; float: left; border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; background-color: #fafafa; }
        .summary-box-right { float: right; }
        .clear { clear: both; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .fw-bold { font-weight: bold; }
        .badge { padding: 3px 6px; border-radius: 10px; font-size: 9px; }
        .badge-success { background: #d1e7dd; color: #0f5132; }
        .badge-warning { background: #fff3cd; color: #664d03; }
        .badge-danger { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; font-size: 22px;">PAYROLL REPORT</h2>
        <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Period: {{ $month }} {{ $year }}</p>
    </div>

    <div>
        <div class="summary-box">
            <strong>Summary Information</strong><br><br>
            Total Employees: {{ $totalEmployees }}<br>
            Pending Approvals: {{ $pendingApprovals }}<br>
            Report Date: {{ date('d F Y') }}
        </div>
        <div class="summary-box summary-box-right">
            <strong>Financial Overview</strong><br><br>
            <table style="margin:0; border:none;">
                <tr><td style="border:none; padding:2px;">Total Gross Salary:</td><td style="border:none; padding:2px;" class="text-right">Rp {{ number_format($totalGross, 0, ',', '.') }}</td></tr>
                <tr><td style="border:none; padding:2px;">Total Deductions:</td><td style="border:none; padding:2px;" class="text-right text-danger">- Rp {{ number_format($totalDeductions, 0, ',', '.') }}</td></tr>
                <tr><td style="border:none; padding:2px; font-weight:bold;">Total Net Salary:</td><td style="border:none; padding:2px; font-weight:bold;" class="text-right text-success">Rp {{ number_format($totalNet, 0, ',', '.') }}</td></tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <h4 style="margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Employee Details</h4>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Employee Name</th>
                <th>Position</th>
                <th class="text-right">Gross Salary</th>
                <th class="text-right">Tax & Deductions</th>
                <th class="text-right">Net Salary</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $p->employee->employee_name }}</strong><br><span style="font-size: 9px; color: #666;">NIK: {{ $p->employee->employee_nik ?? '-' }}</span></td>
                    <td>{{ $p->employee->position->position_type ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($p->employee->employee_basic_salary + ($p->transactional ? $p->transactional->transactional_total : 0), 0, ',', '.') }}</td>
                    <td class="text-right text-danger">- Rp {{ number_format($p->payroll_tax + 0 + $p->payroll_total_late, 0, ',', '.') }}</td>
                    <td class="text-right fw-bold text-success">Rp {{ number_format($p->payroll_net_salary, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($p->presence_status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($p->presence_status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">No payroll data available for this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0;">
                <th colspan="3" class="text-right">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalGross, 0, ',', '.') }}</th>
                <th class="text-right text-danger">- Rp {{ number_format($totalDeductions, 0, ',', '.') }}</th>
                <th class="text-right fw-bold text-success">Rp {{ number_format($totalNet, 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; width: 250px; float: right; text-align: center;">
        <p>Verified By,</p>
        <br><br><br><br>
        <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">HR Manager</p>
        <p style="margin: 0; font-size: 10px; color: #666;">PT Mitra Skripsi HRIS</p>
    </div>

</body>
</html>
