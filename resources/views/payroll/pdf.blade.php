<!DOCTYPE html>
<html>
<head>
    <title>Payroll Slip {{ $month }} {{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .employee-info { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f8f9fa; text-align: left; }
        .text-right { text-align: right; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; background: #f0f0f0; padding: 5px; text-transform: uppercase; font-size: 11px; color: #555; }
        .page-break { page-break-after: always; }
        .totals { margin-top: 20px; width: 40%; float: right; border: 1px solid #000; padding: 10px; background-color: #f8f9fa; }
        .totals table { border: none; margin-bottom: 0; }
        .totals td { border: none; padding: 6px; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    @forelse ($payrolls as $index => $payroll)
        <div class="header">
            <h2 style="margin: 0; font-size: 24px;">SALARY SLIP</h2>
            <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Period: {{ $month }} {{ $year }}</p>
        </div>

        <div class="employee-info">
            <table style="border: none; margin-bottom: 0;">
                <tr>
                    <td style="border: none; width: 120px; padding: 4px;"><strong>Employee ID</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->employee->employee_nik ?? str_pad($payroll->employee_id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="border: none; width: 120px; padding: 4px;"><strong>Status PPh21</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->employee->tax->tax_status ?? 'TK/0' }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 4px;"><strong>Name</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->employee->employee_name }}</td>
                    <td style="border: none; padding: 4px;"><strong>Join Date</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->employee->created_at ? $payroll->employee->created_at->format('d M Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 4px;"><strong>Position</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->employee->position->position_type ?? '-' }}</td>
                    <td style="border: none; padding: 4px;"><strong>Effective Work Days</strong></td>
                    <td style="border: none; padding: 4px;">: {{ $payroll->hke }} Days</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 4px;"><strong>Daily Rate</strong></td>
                    <td style="border: none; padding: 4px;">: Rp {{ number_format($payroll->daily_rate, 0, ',', '.') }}</td>
                    <td style="border: none; padding: 4px;"><strong>Attendance</strong></td>
                    <td style="border: none; padding: 4px; font-size: 11px;">: Hadir: {{ $payroll->payroll_total_attendance }} Days</td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right" style="width: 150px;">Amount (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2" class="section-title">Earnings</td>
                </tr>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($payroll->employee->employee_basic_salary, 0, ',', '.') }}</td>
                </tr>
                @if($payroll->transactional && $payroll->transactional->transactional_thr > 0)
                <tr>
                    <td>THR</td>
                    <td class="text-right">{{ number_format($payroll->transactional->transactional_thr, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->transactional && $payroll->transactional->transactional_bonus > 0)
                <tr>
                    <td>Target Bonus</td>
                    <td class="text-right">{{ number_format($payroll->transactional->transactional_bonus, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->transactional && $payroll->transactional->transactional_overtime > 0)
                <tr>
                    <td>Overtime</td>
                    <td class="text-right">{{ number_format($payroll->transactional->transactional_overtime, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->payroll_reimburse_total > 0)
                <tr>
                    <td>Reimbursement</td>
                    <td class="text-right">{{ number_format($payroll->payroll_reimburse_total, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td class="text-right">Total Gross Salary</td>
                    <td class="text-right">{{ number_format($payroll->employee->employee_basic_salary + ($payroll->transactional ? $payroll->transactional->transactional_total : 0), 0, ',', '.') }}</td>
                </tr>
                
                <tr>
                    <td colspan="2" class="section-title">Deductions</td>
                </tr>
                <tr>
                    <td>PPh21 Tax Deduction</td>
                    <td class="text-right text-danger">- {{ number_format($payroll->payroll_tax, 0, ',', '.') }}</td>
                </tr>
                @if($payroll->transactional && $payroll->transactional->transactional_bpjs > 0)
                <tr>
                    <td>BPJS Deduction</td>
                    <td class="text-right text-danger">- {{ number_format($payroll->transactional->transactional_bpjs, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->payroll_total_late > 0)
                <tr>
                    <td>Lateness Penalty</td>
                    <td class="text-right text-danger">- {{ number_format($payroll->payroll_total_late, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td class="text-right">Total Deductions</td>
                    <td class="text-right text-danger">- {{ number_format($payroll->payroll_tax + ($payroll->transactional ? $payroll->transactional->transactional_bpjs : 0) + $payroll->payroll_total_late, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td><strong>Total Gross</strong></td>
                    <td class="text-right">Rp {{ number_format($payroll->employee->employee_basic_salary + ($payroll->transactional ? $payroll->transactional->transactional_total : 0), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Tax & Penalties</strong></td>
                    <td class="text-right text-danger">- Rp {{ number_format($payroll->payroll_tax + ($payroll->transactional ? $payroll->transactional->transactional_bpjs : 0) + $payroll->payroll_total_late, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2"><hr style="border: 0; border-top: 1px solid #ccc;"></td>
                </tr>
                <tr style="font-size: 14px;">
                    <td><strong>TAKE HOME PAY</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($payroll->payroll_net_salary, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <div style="margin-top: 80px; width: 250px; float: right; text-align: center;">
            <p>Jakarta, {{ date('d F Y') }}</p>
            <br><br><br><br>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">HR Manager</p>
            <p style="margin: 0; font-size: 10px; color: #666;">PT Mitra Skripsi HRIS</p>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <div style="text-align: center; padding: 50px;">
            <h3>No payroll data found for this period.</h3>
        </div>
    @endforelse
</body>
</html>
