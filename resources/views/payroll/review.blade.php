@extends('layouts.admin')

@section('title', 'Review Payroll - HRIS')

@section('content')
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="font-bold text-2xl text-gray-800 mb-1">Review Payroll</h3>
            <p class="text-gray-500 text-sm">Please verify the calculated payroll details before finalizing.</p>
        </div>
        <div>
            <a href="{{ route('payroll.index') }}" class="bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-2 px-4 rounded-xl shadow-sm transition-colors text-sm no-underline inline-flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> Back to Payroll
            </a>
        </div>
    </div>

    <!-- Period Info -->
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h6 class="font-bold text-blue-800 m-0 text-lg">Period: {{ $monthName }} {{ $year }}</h6>
            <div class="text-blue-600 text-sm mt-1">
                {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }} &bull; {{ $hke }} Working Days
            </div>
        </div>
        <div class="mt-4 md:mt-0 md:text-right">
            <h6 class="font-bold text-blue-800 m-0 text-lg">{{ count($calculatedData) }} Employees</h6>
            <div class="text-blue-600 text-sm mt-1">Ready to generate</div>
        </div>
    </div>

    <!-- Details Table -->
    <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Basic Salary</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Additions</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Deductions</th>
                        <th class="py-4 px-6 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Net Salary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @foreach ($calculatedData as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-sm text-gray-800">{{ $item['employee']->employee_name }}</div>
                                <div class="text-xs text-gray-500">Hadir: {{ $item['hadir_count'] }} Hari</div>
                            </td>
                            <td class="py-4 px-4 text-right text-sm font-medium text-gray-600">
                                Rp {{ number_format($item['basic_salary'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-right text-sm">
                                @if($item['overtime'] > 0)
                                    <div class="text-green-600 font-medium text-xs mb-0.5">+ OT: Rp {{ number_format($item['overtime'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['thr'] > 0)
                                    <div class="text-green-600 font-medium text-xs mb-0.5">+ THR: Rp {{ number_format($item['thr'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['target_bonus'] > 0)
                                    <div class="text-green-600 font-medium text-xs mb-0.5">+ Bonus: Rp {{ number_format($item['target_bonus'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['reimbursement'] > 0)
                                    <div class="text-green-600 font-medium text-xs">+ Reimb: Rp {{ number_format($item['reimbursement'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['overtime'] == 0 && $item['thr'] == 0 && $item['target_bonus'] == 0 && $item['reimbursement'] == 0)
                                    <span class="text-gray-400 italic text-xs">None</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right text-sm">
                                @if($item['bpjs'] > 0)
                                    <div class="text-red-500 font-medium text-xs mb-0.5">- BPJS: Rp {{ number_format($item['bpjs'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['tax'] > 0)
                                    <div class="text-red-500 font-medium text-xs mb-0.5">- PPh21: Rp {{ number_format($item['tax'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['denda_terlambat'] > 0)
                                    <div class="text-red-500 font-medium text-xs">- Lates: Rp {{ number_format($item['denda_terlambat'], 0, ',', '.') }}</div>
                                @endif
                                @if($item['bpjs'] == 0 && $item['tax'] == 0 && $item['denda_terlambat'] == 0)
                                    <span class="text-gray-400 italic text-xs">None</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-lg text-gray-800">
                                Rp {{ number_format($item['net_salary'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Final Confirmation -->
    <div class="bg-gray-50 border border-gray-200 p-6 rounded-3xl flex flex-col md:flex-row justify-between items-center shadow-sm gap-4">
        <div>
            <h6 class="font-bold text-gray-800 m-0">Ready to finalize?</h6>
            <p class="text-sm text-gray-500 mb-0">Once generated, payroll statements will be saved and available to employees.</p>
        </div>
        <form action="{{ route('payroll.storeBatch') }}" method="POST" class="w-full md:w-auto">
            @csrf
            <input type="hidden" name="start_date" value="{{ $requestData['start_date'] }}">
            <input type="hidden" name="end_date" value="{{ $requestData['end_date'] }}">
            
            @if(!empty($requestData['employees']))
                @foreach($requestData['employees'] as $empId)
                    <input type="hidden" name="employees[]" value="{{ $empId }}">
                    @if(isset($requestData['bonuses'][$empId]))
                        <input type="hidden" name="bonuses[{{ $empId }}][thr]" value="{{ $requestData['bonuses'][$empId]['thr'] ?? 0 }}">
                        <input type="hidden" name="bonuses[{{ $empId }}][target_bonus]" value="{{ $requestData['bonuses'][$empId]['target_bonus'] ?? 0 }}">
                        <input type="hidden" name="bonuses[{{ $empId }}][overtime]" value="{{ $requestData['bonuses'][$empId]['overtime'] ?? 0 }}">
                    @endif
                @endforeach
            @endif

            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition-colors border-0 cursor-pointer text-sm flex items-center justify-center">
                <i class="bi bi-check-circle-fill mr-2"></i> Confirm & Generate Payroll
            </button>
        </form>
    </div>
@endsection
