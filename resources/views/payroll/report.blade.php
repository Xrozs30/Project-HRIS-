@extends('layouts.admin')

@section('title', 'Payroll Report - HRIS')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <h3 class="font-bold text-2xl text-gray-800 mb-1">Payroll Report</h3>
        <p class="text-gray-500 text-sm mb-0">Comprehensive payroll analysis and reporting</p>
    </div>
    <div class="flex gap-3">
        <a href="#" class="inline-flex items-center text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 font-bold px-4 py-2 rounded-xl transition-colors no-underline">
            <i class="bi bi-file-earmark-spreadsheet mr-2"></i> Export CSV
        </a>
        <a href="{{ route('payroll.report_pdf', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 font-bold px-4 py-2 rounded-xl transition-colors no-underline">
            <i class="bi bi-file-earmark-pdf mr-2"></i> Export PDF
        </a>
    </div>
</div>

<div class="bg-white border-0 shadow-sm rounded-3xl mb-6 overflow-hidden">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-10">
                <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Select Period</label>
                <select class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 opacity-75 cursor-not-allowed" disabled>
                    <option selected>{{ $month }} {{ $year }}</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md opacity-50 cursor-not-allowed border-0">
                    <i class="bi bi-search mr-2"></i> View Report
                </button>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-blue-50 text-blue-600 rounded-2xl flex justify-center items-center w-14 h-14 mr-4 flex-shrink-0">
            <i class="bi bi-people-fill text-2xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Employees</div>
            <h4 class="font-bold text-gray-800 text-xl mb-0">{{ $totalEmployees }}</h4>
        </div>
    </div>
    
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-green-50 text-green-600 rounded-2xl flex justify-center items-center w-14 h-14 mr-4 flex-shrink-0">
            <i class="bi bi-currency-dollar text-2xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Net Salary</div>
            <h5 class="font-bold text-gray-800 text-lg mb-0">Rp {{ number_format($totalNet, 0, ',', '.') }}</h5>
        </div>
    </div>
    
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-yellow-50 text-yellow-600 rounded-2xl flex justify-center items-center w-14 h-14 mr-4 flex-shrink-0">
            <i class="bi bi-clock-history text-2xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Pending Approvals</div>
            <h4 class="font-bold text-gray-800 text-xl mb-0">{{ $pendingApprovals }}</h4>
        </div>
    </div>
    
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-purple-50 text-purple-600 rounded-2xl flex justify-center items-center w-14 h-14 mr-4 flex-shrink-0">
            <i class="bi bi-graph-up text-2xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Avg Attendance Rate</div>
            <h4 class="font-bold text-gray-800 text-xl mb-0">{{ number_format($avgAttendanceRate, 1) }}%</h4>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-8">
        <h6 class="font-bold text-lg text-gray-800 mb-6">Salary Breakdown</h6>
        <div class="flex justify-between items-center mb-4 text-gray-500">
            <span class="font-medium">Total Gross Salary:</span>
            <span class="font-bold text-gray-800">Rp {{ number_format($totalGross, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center mb-5 pb-5 border-b border-gray-100 text-gray-500">
            <span class="font-medium">Total Deductions:</span>
            <span class="font-bold text-red-500">- Rp {{ number_format($totalDeductions, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="font-bold text-gray-800">Total Net Salary:</span>
            <span class="font-bold text-green-600 text-xl">Rp {{ number_format($totalNet, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-8">
        <h6 class="font-bold text-lg text-gray-800 mb-6">Deduction Details</h6>
        <div class="flex justify-between items-center mb-4 text-gray-500 text-sm">
            <span class="font-medium">Attendance Deduction:</span>
            <span class="font-bold">Rp {{ number_format($totalAttendanceDeduction, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center mb-4 text-gray-500 text-sm">
            <span class="font-medium">Tax Deduction (PPh21):</span>
            <span class="font-bold">Rp {{ number_format($totalTaxDeduction, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center text-gray-500 text-sm">
            <span class="font-medium">BPJS Deduction:</span>
            <span class="font-bold">Rp {{ number_format($totalBpjsDeduction, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mt-6">
    <div class="bg-white border-b border-gray-100 px-6 py-4">
        <ul class="flex flex-wrap gap-2" role="tablist">
            <li role="presentation">
                <button class="nav-tab active-tab bg-blue-50 text-blue-600 border border-blue-100 font-bold px-5 py-2.5 rounded-full text-sm transition-colors cursor-pointer" data-target="#all" role="tab" aria-selected="true">All Payrolls ({{ $allPayrolls->count() }})</button>
            </li>
            <li role="presentation">
                <button class="nav-tab bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 font-bold px-5 py-2.5 rounded-full text-sm transition-colors cursor-pointer" data-target="#pending" role="tab" aria-selected="false">Pending ({{ $pendingPayrolls->count() }})</button>
            </li>
            <li role="presentation">
                <button class="nav-tab bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 font-bold px-5 py-2.5 rounded-full text-sm transition-colors cursor-pointer" data-target="#approved" role="tab" aria-selected="false">Approved ({{ $approvedPayrolls->count() }})</button>
            </li>
            <li role="presentation">
                <button class="nav-tab bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 font-bold px-5 py-2.5 rounded-full text-sm transition-colors cursor-pointer" data-target="#rejected" role="tab" aria-selected="false">Rejected ({{ $rejectedPayrolls->count() }})</button>
            </li>
        </ul>
    </div>
    
    <div class="p-0">
        <div id="payrollTabsContent">
            
            @php
                $tabs = [
                    'all' => $allPayrolls,
                    'pending' => $pendingPayrolls,
                    'approved' => $approvedPayrolls,
                    'rejected' => $rejectedPayrolls,
                ];
            @endphp
            
            @foreach($tabs as $tabId => $list)
            <div class="tab-panel {{ $tabId === 'all' ? 'block' : 'hidden' }}" id="{{ $tabId }}" role="tabpanel">
                <form action="{{ route('payroll.approveBatch') }}" method="POST">
                    @csrf
                    
                    @if($tabId === 'pending' && $list->count() > 0 && auth()->user()->employee_role === 'owner')
                    <div class="px-6 py-4 bg-gray-50/80 flex gap-3 border-b border-gray-100">
                        <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-xl shadow-sm transition-colors border-0 cursor-pointer text-sm">Approve Selected</button>
                        <button type="submit" name="action" value="reject" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-5 rounded-xl shadow-sm transition-colors border-0 cursor-pointer text-sm">Reject Selected</button>
                    </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    @if($tabId === 'pending')
                                        @if(auth()->user()->employee_role === 'owner')
                                        <th class="py-4 pl-6 pr-2 w-10"><input type="checkbox" class="rounded text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 w-4 h-4" onchange="toggleCheckboxes(this, '{{ $tabId }}')"></th>
                                        @else
                                        <th class="py-4 pl-6 pr-2 w-10"></th>
                                        @endif
                                    @endif
                                    <th class="{{ $tabId !== 'pending' ? 'pl-6' : '' }} py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Period</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Attendance</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Gross Salary</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Tax (PPh)</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Other Deductions</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Net Salary</th>
                                    <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Status</th>
                                    <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/50">
                                @forelse($list as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    @if($tabId === 'pending')
                                        @if(auth()->user()->employee_role === 'owner')
                                        <td class="py-4 pl-6 pr-2">
                                            <input type="checkbox" name="payroll_ids[]" value="{{ $p->payroll_id }}" class="rounded text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 w-4 h-4 cb-{{ $tabId }}">
                                        </td>
                                        @else
                                        <td class="py-4 pl-6 pr-2"></td>
                                        @endif
                                    @endif
                                    <td class="{{ $tabId !== 'pending' ? 'pl-6' : '' }} py-4 px-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-bold flex justify-center items-center mr-3 flex-shrink-0 border border-blue-100">
                                                {{ substr($p->employee->employee_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm text-gray-800">{{ $p->employee->employee_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm font-bold text-gray-800">01 {{ substr($p->payroll_periode_month, 0, 3) }} {{ $p->payroll_periode_year }} - 30 {{ substr($p->payroll_periode_month, 0, 3) }} {{ $p->payroll_periode_year }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">01 {{ substr($p->payroll_periode_month, 0, 3) }} - 30 {{ substr($p->payroll_periode_month, 0, 3) }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @php
                                            $hadir = $p->payroll_total_attendance ?? 0;
                                            $rate = $hke > 0 ? ($hadir / $hke) * 100 : 0;
                                            
                                            $basic = $p->employee->employee_basic_salary ?? ($p->employee->basic_salary ?? 0);
                                            $daily = $hke > 0 ? $basic / $hke : 0;
                                            $trans = $p->transactional ? $p->transactional->transactional_total : 0;
                                            $reimb = $p->payroll_reimburse_total ?? 0;
                                            $actualGross = ($hadir * $daily) + $trans + $reimb;
                                        @endphp
                                        <div class="inline-block {{ $rate < 100 ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : 'bg-green-50 text-green-600 border-green-100' }} rounded-full px-3 py-1 text-xs font-bold border">{{ number_format($rate, 1) }}%</div>
                                        <div class="text-xs text-gray-400 mt-1">{{ $hadir }}/{{ $hke }} days</div>
                                    </td>
                                    <td class="py-4 px-4 text-right font-bold text-gray-800">Rp {{ number_format($actualGross, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4 text-right text-red-500">
                                        <div class="font-medium">- Rp {{ number_format($p->payroll_tax, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">PPh 21</div>
                                    </td>
                                    <td class="py-4 px-4 text-right text-red-500">
                                        <div class="font-medium">- Rp {{ number_format(0 + $p->payroll_total_late, 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">Lateness & Alpha</div>
                                    </td>
                                    <td class="py-4 px-4 text-right font-bold text-green-600">Rp {{ number_format($p->payroll_net_salary, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4 text-right">
                                        @if($p->payroll_status == 'pending')
                                            <span class="inline-flex items-center bg-yellow-50 text-yellow-600 px-3 py-1.5 rounded-full text-xs font-bold border border-yellow-100"><i class="bi bi-clock mr-1"></i> Pending</span>
                                        @elseif($p->payroll_status == 'approved')
                                            <span class="inline-flex items-center bg-green-50 text-green-600 px-3 py-1.5 rounded-full text-xs font-bold border border-green-100"><i class="bi bi-check-circle mr-1"></i> Approved</span>
                                        @else
                                            <span class="inline-flex items-center bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-xs font-bold border border-red-100"><i class="bi bi-x-circle mr-1"></i> Rejected</span>
                                        @endif
                                    </td>
                                    <td class="py-4 pr-6 pl-4 text-right">
                                        @if($p->payroll_status == 'approved')
                                            <a href="{{ route('payroll.pdf', ['month' => $p->payroll_periode_month, 'year' => $p->payroll_periode_year, 'employee_id' => $p->employee_id]) }}" class="inline-flex items-center text-red-600 border border-red-200 hover:bg-red-50 font-bold px-3 py-1.5 rounded-full text-xs transition-colors no-underline">
                                                <i class="bi bi-file-earmark-pdf mr-1"></i> PDF
                                            </a>
                                        @else
                                            <span class="text-gray-300 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $tabId === 'pending' ? 10 : 9 }}" class="text-center py-12 text-gray-400 font-medium">No {{ $tabId }} payrolls found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            @endforeach
            
        </div>
    </div>
</div>

<script>
    function toggleCheckboxes(source, tab) {
        const checkboxes = document.querySelectorAll('.cb-' + tab);
        checkboxes.forEach(cb => cb.checked = source.checked);
    }
    
    // Vanilla JS for Tabs
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.nav-tab');
        const tabContents = document.querySelectorAll('.tab-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active classes
                tabs.forEach(t => {
                    t.classList.remove('active-tab', 'bg-blue-50', 'text-blue-600', 'border-blue-100');
                    t.classList.add('bg-white', 'text-gray-500', 'border-gray-200', 'hover:bg-gray-50');
                    t.setAttribute('aria-selected', 'false');
                });
                
                // Hide all contents
                tabContents.forEach(c => {
                    c.classList.remove('block');
                    c.classList.add('hidden');
                });

                // Add active classes to selected
                tab.classList.remove('bg-white', 'text-gray-500', 'border-gray-200', 'hover:bg-gray-50');
                tab.classList.add('active-tab', 'bg-blue-50', 'text-blue-600', 'border-blue-100');
                tab.setAttribute('aria-selected', 'true');

                // Show target content
                const target = document.querySelector(tab.dataset.target);
                if (target) {
                    target.classList.remove('hidden');
                    target.classList.add('block');
                }
            });
        });
    });
</script>
@endsection
