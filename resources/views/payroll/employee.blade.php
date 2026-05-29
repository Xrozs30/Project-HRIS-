@extends('layouts.admin')

@section('title', 'My Payroll - HRIS')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <h3 class="font-bold text-2xl text-gray-800 mb-1">My Payroll</h3>
        <p class="text-gray-500 text-sm mb-0">View your salary history and payroll details</p>
    </div>
    
    <div class="flex items-center gap-3">
        <label class="text-gray-500 text-sm font-bold whitespace-nowrap">Select Period:</label>
        <form id="periodForm" action="{{ route('payroll.index') }}" method="GET">
            <div class="relative">
                <select name="period" class="appearance-none bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm min-w-[150px] font-medium" onchange="document.getElementById('periodForm').submit();">
                    <option value="all" {{ $selectedPeriod == 'all' ? 'selected' : '' }}>All Time</option>
                    @foreach($periods as $p)
                        @php
                            $value = $p->year . '-' . $p->month;
                            $label = $p->month . ' ' . $p->year;
                        @endphp
                        <option value="{{ $value }}" {{ $selectedPeriod == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Current Gross -->
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-blue-50 text-blue-600 rounded-2xl flex justify-center items-center w-12 h-12 mr-4 flex-shrink-0">
            <i class="bi bi-cash-coin text-xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Current Gross</div>
            <h5 class="font-bold text-gray-800 text-lg mb-0">Rp {{ number_format($currentGross, 0, ',', '.') }}</h5>
        </div>
    </div>
    <!-- Current Net -->
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-green-50 text-green-600 rounded-2xl flex justify-center items-center w-12 h-12 mr-4 flex-shrink-0">
            <i class="bi bi-currency-dollar text-xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Current Net</div>
            <h5 class="font-bold text-gray-800 text-lg mb-0">Rp {{ number_format($currentNet, 0, ',', '.') }}</h5>
        </div>
    </div>
    <!-- Average Net -->
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-purple-50 text-purple-600 rounded-2xl flex justify-center items-center w-12 h-12 mr-4 flex-shrink-0">
            <i class="bi bi-graph-up text-xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Average Net</div>
            <h5 class="font-bold text-gray-800 text-lg mb-0">Rp {{ number_format($averageNet, 0, ',', '.') }}</h5>
        </div>
    </div>
    <!-- Total Records -->
    <div class="bg-white border-0 shadow-sm rounded-3xl h-full p-6 flex items-center">
        <div class="bg-yellow-50 text-yellow-600 rounded-2xl flex justify-center items-center w-12 h-12 mr-4 flex-shrink-0">
            <i class="bi bi-clock-history text-xl"></i>
        </div>
        <div>
            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Records</div>
            <h5 class="font-bold text-gray-800 text-lg mb-0">{{ $totalRecords }}</h5>
        </div>
    </div>
</div>

<!-- Payroll Table -->
<div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
    <div class="border-b border-gray-100 bg-white py-4 px-6">
        <h6 class="font-bold text-gray-800 text-lg m-0">Payroll History</h6>
    </div>
    <div class="p-0">
        @if($payrolls->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Basic Salary</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Allowances</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Deductions</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Net Salary</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @foreach($payrolls as $payroll)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6 font-bold text-sm text-gray-800">{{ $payroll->payroll_periode_month }} {{ $payroll->payroll_periode_year }}</td>
                        <td class="py-4 px-6 text-right text-gray-500 font-medium">Rp {{ number_format($payroll->employee->employee_basic_salary, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right text-green-600 font-medium">+ Rp {{ number_format($payroll->transactional ? $payroll->transactional->transactional_total : 0, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right text-red-500 font-medium">- Rp {{ number_format($payroll->payroll_tax + $payroll->payroll_total_late, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right font-bold text-gray-800">Rp {{ number_format($payroll->payroll_net_salary, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('payroll.pdf', ['month' => $payroll->payroll_periode_month, 'year' => $payroll->payroll_periode_year]) }}" class="inline-flex items-center text-blue-600 border border-blue-600 hover:bg-blue-50 font-bold px-4 py-1.5 rounded-full text-sm transition-colors no-underline">
                                <i class="bi bi-download mr-2"></i> PDF
                            </a>
                        </td>
                    </tr>
                    <tr class="bg-blue-50/30 border-b border-gray-100">
                        <td colspan="6" class="px-6 py-3">
                            <div class="flex flex-wrap gap-x-8 gap-y-2 text-xs text-gray-600">
                                <div><span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Hadir:</span> {{ $payroll->payroll_total_attendance }} hari</div>
                                <div><span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Terlambat:</span> <span class="text-red-500 font-medium">(-Rp{{ number_format($payroll->payroll_total_late, 0, ',', '.') }})</span></div>
                                <div><span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Pajak (PPh21):</span> <span class="text-red-500 font-medium">(-Rp{{ number_format($payroll->payroll_tax, 0, ',', '.') }})</span></div>
                                @if($payroll->transactional)
                                <div><span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">BPJS Potongan:</span> <span class="text-red-500 font-medium">(-Rp{{ number_format($payroll->transactional->transactional_bpjs, 0, ',', '.') }})</span></div>
                                <div><span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Lembur:</span> <span class="text-green-600 font-medium">(+Rp{{ number_format($payroll->transactional->transactional_overtime, 0, ',', '.') }})</span></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mb-4">
                <i class="bi bi-cash-stack text-gray-200 text-6xl block"></i>
            </div>
            <h6 class="font-bold text-gray-700 text-lg mb-2">No Payroll Data Found</h6>
            <p class="text-gray-400 text-sm">No payroll records found for the selected period.</p>
        </div>
        @endif
    </div>
</div>
@endsection
