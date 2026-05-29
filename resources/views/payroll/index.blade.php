@extends('layouts.admin')

@section('title', 'Payroll - HRIS')

@section('content')
    <div class="mb-6">
        <h3 class="font-bold text-2xl text-gray-800 mb-1">Payroll Archives</h3>
        <p class="text-gray-500 text-sm">Download and manage monthly payroll statements.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-center" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="text-green-700 hover:text-green-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-start" role="alert">
            <ul class="list-disc pl-5 m-0 mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if(auth()->user()->employee_role === 'hr')
    <div class="bg-gray-50/80 border border-gray-100 rounded-3xl p-6 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl mr-4 flex-shrink-0 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </div>
            <div>
                <h5 class="font-bold text-lg text-gray-800 mb-1">Custom Payroll Generator</h5>
                <p class="text-gray-500 text-sm mb-0">Select period to generate payroll.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">From</label>
                <input type="date" id="filter_start" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow" max="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">To</label>
                <input type="date" id="filter_end" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow" max="{{ date('Y-m-d') }}">
            </div>
            <div>
                <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow-sm transition-colors border-0 cursor-pointer" onclick="openGenerator()">
                    <i class="bi bi-plus-lg mr-2"></i> Generate Payroll
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
        <div class="bg-white border-b border-gray-100 py-4 px-6 flex justify-between items-center">
            <h6 class="font-bold text-gray-800 text-lg m-0">Monthly Payroll Statements</h6>
            <button class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold py-1.5 px-3 rounded-full text-xs shadow-sm transition-colors cursor-pointer" onclick="location.reload();">
                <i class="bi bi-trash"></i> Reset Table
            </button>
        </div>
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Month / Period</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-center">Total Employees</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Gross Salary</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Total PPh Deduction</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Total Net Salary</th>
                            <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/50">
                        @forelse ($payrollGroups as $group)
                        @php
                            $periodRecords = \App\Models\Payroll::with(['employee', 'transactional'])
                                            ->where('payroll_periode_month', $group->month)
                                            ->where('payroll_periode_year', $group->year)
                                            ->get();
                            $totalGross = $periodRecords->sum(fn($p) => ($p->employee->employee_basic_salary ?? 0) + ($p->transactional ? $p->transactional->transactional_total : 0));
                            $totalDeduction = $periodRecords->sum(fn($p) => $p->payroll_tax + $p->payroll_total_late);
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-6 pr-4 font-bold text-sm text-gray-800">{{ $group->month }} {{ $group->year }}</td>
                            <td class="py-4 px-4 text-center text-gray-600">{{ $group->total_employees }}</td>
                            <td class="py-4 px-4 text-right text-gray-500 font-medium">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-right text-red-500 font-medium">- Rp {{ number_format($totalDeduction, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-right font-bold text-green-600">Rp {{ number_format($group->total_salary, 0, ',', '.') }}</td>
                            <td class="py-4 pr-6 pl-4 text-right">
                                <a href="{{ route('payroll.report', ['month' => $group->month, 'year' => $group->year]) }}" class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 font-bold px-4 py-1.5 rounded-full text-sm transition-colors shadow-sm no-underline">
                                    <i class="bi bi-eye mr-2"></i> View Report
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400 font-medium">No payroll data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Backdrop for Offcanvas and Modal -->
    <div id="tw-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Offcanvas Generator Tailwind -->
    <div id="tw-generatorOffcanvas" class="fixed inset-y-0 right-0 z-50 w-full md:w-[800px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h5 class="font-bold text-lg text-gray-800 m-0">Select Employees for Payroll</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl" onclick="closeGenerator()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto flex flex-col">
            <form action="{{ route('payroll.reviewBatch') }}" method="POST" id="payrollBatchForm" class="flex flex-col h-full">
                @csrf
                <input type="hidden" name="start_date" id="input_start_date">
                <input type="hidden" name="end_date" id="input_end_date">

                <div class="p-4 bg-white border-b border-gray-100 flex-shrink-0">
                    <div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-xl flex justify-between items-center">
                        <span class="text-sm font-bold">Period: <span id="display_period" class="font-normal"></span></span>
                    </div>
                </div>

                <div class="flex-1 overflow-x-auto p-4">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-2 pl-2 w-10">
                                    <input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="rounded text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                </th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Bank & Account</th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Basic Salary</th>
                                <th class="py-2 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @foreach ($employees as $emp)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 pl-2">
                                    <input type="checkbox" name="employees[]" value="{{ $emp->employee_id }}" class="emp-checkbox rounded text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="py-3 px-3"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">#{{ $emp->employee_nik }}</span></td>
                                <td class="py-3 px-3"><div class="font-bold text-sm text-gray-800">{{ $emp->employee_name }}</div></td>
                                <td class="py-3 px-3"><div class="text-xs text-gray-500">{{ $emp->position_type ?? 'Staff' }}</div></td>
                                <td class="py-3 px-3">
                                    @if($emp->employee_bank_name && $emp->employee_bank_number)
                                        <div class="text-xs font-bold text-gray-800">{{ $emp->employee_bank_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $emp->employee_bank_number }}</div>
                                    @else
                                        <div class="text-xs text-gray-400 italic">Not Set</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right text-sm font-bold text-gray-800">Rp {{ number_format($emp->employee_basic_salary ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right">
                                    <button type="button" class="bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors border-0 cursor-pointer" onclick="openBonusModal('{{ $emp->employee_id }}', '{{ addslashes($emp->employee_name) }}', {{ $emp->employee_basic_salary ?? 0 }}, '{{ $emp->tax->tax_status ?? 'TK/0' }}')">
                                        <i class="bi bi-pencil-square mr-1"></i> Edit Allowances
                                    </button>

                                    <!-- Hidden Inputs for Bonuses -->
                                    <input type="hidden" name="bonuses[{{ $emp->employee_id }}][thr]" id="thr_{{ $emp->employee_id }}" value="0">
                                    <input type="hidden" name="bonuses[{{ $emp->employee_id }}][target_bonus]" id="target_{{ $emp->employee_id }}" value="0">
                                    <input type="hidden" name="bonuses[{{ $emp->employee_id }}][overtime]" id="overtime_{{ $emp->employee_id }}" value="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200 flex-shrink-0 flex justify-between items-center mt-auto">
                    <div class="text-sm text-gray-500 font-medium"><span id="selectedCount" class="font-bold text-gray-800">0</span> selected</div>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors border-0 cursor-pointer" onclick="submitBatch()">
                        Next <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bonus Modal Tailwind -->
    <div id="tw-bonusModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="tw-bonusModalContent">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h6 class="font-bold text-lg text-gray-800 m-0">Add Allowances for <span id="bonusEmpName" class="text-blue-600"></span></h6>
                <button type="button" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl" onclick="closeBonusModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(100vh-120px)]">
                <input type="hidden" id="activeEmpId">
                <input type="hidden" id="activeBasicSalary">
                <input type="hidden" id="activePphStatus">

                <div class="flex justify-between mb-6 pb-4 border-b border-gray-100 items-center">
                    <span class="text-gray-500 font-bold text-sm">Basic Salary:</span>
                    <span class="font-bold text-xl text-gray-800" id="displayBasicSalary">Rp 0</span>
                </div>

                <div class="mb-6">
                    <label class="block font-bold text-sm text-gray-800 mb-4">Select Bonus Types</label>
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="cb_thr" class="sr-only bonus-checkbox" value="thr" onchange="toggleBonusInput('thr')">
                                <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner transition-colors border border-gray-200 toggle-bg group-hover:bg-gray-300"></div>
                                <div class="absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition-transform toggle-dot"></div>
                            </div>
                            <span class="ml-3 text-sm text-gray-700 font-medium">THR (Tunjangan Hari Raya)</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="cb_target" class="sr-only bonus-checkbox" value="target" onchange="toggleBonusInput('target')">
                                <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner transition-colors border border-gray-200 toggle-bg group-hover:bg-gray-300"></div>
                                <div class="absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition-transform toggle-dot"></div>
                            </div>
                            <span class="ml-3 text-sm text-gray-700 font-medium">Target Achievement</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" id="cb_overtime" class="sr-only bonus-checkbox" value="overtime" onchange="toggleBonusInput('overtime')">
                                <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner transition-colors border border-gray-200 toggle-bg group-hover:bg-gray-300"></div>
                                <div class="absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition-transform toggle-dot"></div>
                            </div>
                            <span class="ml-3 text-sm text-gray-700 font-medium">Overtime</span>
                        </label>
                    </div>
                </div>

                <!-- Dynamic Inputs container based on selection -->
                <div id="dynamicInputsContainer" class="bg-gray-50 p-5 rounded-2xl border border-gray-100 mb-6 shadow-sm hidden">
                    <h6 class="font-bold mb-4 text-xs tracking-wider text-gray-500 uppercase">Enter Nominal Values</h6>
                    <div class="mb-4 hidden" id="wrap_thr">
                        <label class="block text-xs font-bold text-gray-700 mb-2">THR (Rp)</label>
                        <input type="number" class="w-full bg-white border border-gray-200 text-gray-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bonus-input" id="modal_thr" value="0" oninput="calculateTotal()">
                    </div>
                    <div class="mb-4 hidden" id="wrap_target">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Target Achievement (Rp)</label>
                        <input type="number" class="w-full bg-white border border-gray-200 text-gray-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bonus-input" id="modal_target" value="0" oninput="calculateTotal()">
                    </div>
                    <div class="mb-0 hidden" id="wrap_overtime">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Overtime (Rp)</label>
                        <input type="number" class="w-full bg-white border border-gray-200 text-gray-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bonus-input" id="modal_overtime" value="0" oninput="calculateTotal()">
                    </div>
                </div>

                <!-- Estimates -->
                <div class="bg-blue-50 p-5 rounded-2xl border border-blue-100 mb-6">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-500 font-bold">PTKP / PPh Status:</span>
                        <span class="text-xs font-bold text-blue-600" id="displayPphStatus">TK/0</span>
                    </div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-500 font-bold">Gross Salary (Before Tax):</span>
                        <span class="text-xs font-bold text-gray-800" id="displayGrossSalary">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs text-gray-500 font-bold">Est. PPh21 Deduction:</span>
                        <span class="text-xs font-bold text-red-500" id="displayEstimatedPph">- Rp 0</span>
                    </div>
                    <hr class="border-blue-200/50 my-3">
                    <div class="flex justify-between items-center text-blue-700">
                        <span class="font-bold text-sm">Estimated Total Net Salary:</span>
                        <span class="font-bold text-lg" id="displayTotalSalary">Rp 0</span>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-sm transition-colors border-0 cursor-pointer" onclick="saveBonuses()">Save Changes</button>
                    <button type="button" class="w-full bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-3 rounded-xl transition-colors cursor-pointer" onclick="closeBonusModal()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Toggle Switch Styles for Tailwind */
        input:checked + .toggle-bg {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        input:checked + .toggle-bg + .toggle-dot {
            transform: translateX(100%);
        }
    </style>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.emp-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        // Initialize date constraints logic
        const startInput = document.getElementById('filter_start');
        const endInput = document.getElementById('filter_end');

        if(startInput && endInput) {
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
                if(endInput.value && endInput.value < this.value) {
                    endInput.value = this.value;
                }
            });
            
            endInput.addEventListener('change', function() {
                startInput.max = this.value;
                let today = new Date().toISOString().split('T')[0];
                if(this.value > today) this.value = today;
                if(startInput.value > today) startInput.value = today;
            });
        }
    });

    // --- Vanilla JS Offcanvas & Modal Handlers ---
    const backdrop = document.getElementById('tw-backdrop');
    const offcanvas = document.getElementById('tw-generatorOffcanvas');
    const modal = document.getElementById('tw-bonusModal');
    const modalContent = document.getElementById('tw-bonusModalContent');

    function showBackdrop() {
        backdrop.classList.remove('hidden');
        // Small delay to allow display flex/block to apply before opacity transition
        setTimeout(() => { backdrop.classList.remove('opacity-0'); backdrop.classList.add('opacity-100'); }, 10);
    }
    function hideBackdrop() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        setTimeout(() => { backdrop.classList.add('hidden'); }, 300);
    }

    function openGenerator() {
        const start = document.getElementById('filter_start').value;
        const end = document.getElementById('filter_end').value;

        if (!start || !end) {
            alert('Please select From and To dates first.');
            return;
        }

        if (start > end) {
            alert('From date cannot be greater than To date.');
            return;
        }

        document.getElementById('input_start_date').value = start;
        document.getElementById('input_end_date').value = end;
        document.getElementById('display_period').innerText = start + ' to ' + end;

        // Reset all overtime hidden fields to 0 before fetching
        document.querySelectorAll('input[id^="overtime_"]').forEach(el => el.value = 0);

        // Fetch auto-calculated overtime
        fetch(`{{ route('payroll.autoOvertime') }}?start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(data => {
                if (data && !data.error) {
                    for (const [userId, amount] of Object.entries(data)) {
                        const overtimeInput = document.getElementById('overtime_' + userId);
                        if (overtimeInput) {
                            overtimeInput.value = amount;
                        }
                    }
                }
            })
            .catch(err => console.error('Error fetching overtime:', err))
            .finally(() => {
                showBackdrop();
                offcanvas.classList.remove('translate-x-full');
                offcanvas.classList.add('translate-x-0');
                document.body.style.overflow = 'hidden';
            });
    }

    function closeGenerator() {
        offcanvas.classList.remove('translate-x-0');
        offcanvas.classList.add('translate-x-full');
        hideBackdrop();
        document.body.style.overflow = '';
    }

    function closeBonusModal() {
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        setTimeout(() => { 
            modal.classList.add('hidden'); 
            // If Offcanvas is still open, keep backdrop
            if(offcanvas.classList.contains('translate-x-full')) {
                // don't hide backdrop completely, just return
            } else {
                hideBackdrop();
                document.body.style.overflow = '';
            }
        }, 300);
    }
    // --- End UI Handlers ---

    function toggleBonusInput(type) {
        const isChecked = document.getElementById(`cb_${type}`).checked;
        const wrapper = document.getElementById(`wrap_${type}`);
        const input = document.getElementById(`modal_${type}`);

        if(isChecked) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
            input.value = 0;
        }

        // Show/Hide container if any checkbox is checked
        const anyChecked = Array.from(document.querySelectorAll('.bonus-checkbox')).some(cb => cb.checked);
        const container = document.getElementById('dynamicInputsContainer');
        if(anyChecked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }

        calculateTotal();
    }

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function calculatePph21(grossMonthly, status) {
        status = status ? status.toUpperCase() : 'TK/0';
        let ptkp = 54000000;
        if(status === 'K/0') ptkp = 58500000;
        else if(status === 'K/1') ptkp = 63000000;
        else if(status === 'K/2') ptkp = 67500000;
        else if(status === 'K/3') ptkp = 72000000;

        let grossYearly = grossMonthly * 12;
        let taxable = grossYearly - ptkp;
        
        if (taxable <= 0) return 0;

        let taxYearly = 0;
        if (taxable <= 60000000) {
            taxYearly = taxable * 0.05;
        } else if (taxable <= 250000000) {
            taxYearly = (60000000 * 0.05) + ((taxable - 60000000) * 0.15);
        } else if (taxable <= 500000000) {
            taxYearly = (60000000 * 0.05) + (190000000 * 0.15) + ((taxable - 250000000) * 0.25);
        } else {
            taxYearly = (60000000 * 0.05) + (190000000 * 0.15) + (250000000 * 0.25) + ((taxable - 500000000) * 0.30);
        }

        return Math.round(taxYearly / 12);
    }

    function calculateTotal() {
        const basic = parseFloat(document.getElementById('activeBasicSalary').value) || 0;
        const thr = parseFloat(document.getElementById('modal_thr').value) || 0;
        const target = parseFloat(document.getElementById('modal_target').value) || 0;
        const overtime = parseFloat(document.getElementById('modal_overtime').value) || 0;
        const pphStatus = document.getElementById('activePphStatus').value;

        const grossMonthly = basic + thr + target + overtime;
        
        const pphDeduction = calculatePph21(grossMonthly, pphStatus);
        const netSalary = grossMonthly - pphDeduction;

        document.getElementById('displayGrossSalary').innerText = formatRupiah(grossMonthly);
        document.getElementById('displayEstimatedPph').innerText = '- ' + formatRupiah(pphDeduction);
        document.getElementById('displayTotalSalary').innerText = formatRupiah(netSalary);
    }

    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.emp-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.emp-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = count;
        
        const allCount = document.querySelectorAll('.emp-checkbox').length;
        document.getElementById('checkAll').checked = (count === allCount && allCount > 0);
    }

    function openBonusModal(empId, empName, basicSalary, pphStatus) {
        document.getElementById('activeEmpId').value = empId;
        document.getElementById('bonusEmpName').innerText = empName;
        document.getElementById('activeBasicSalary').value = basicSalary || 0;
        document.getElementById('activePphStatus').value = pphStatus || 'TK/0';
        
        document.getElementById('displayBasicSalary').innerText = formatRupiah(basicSalary || 0);
        document.getElementById('displayPphStatus').innerText = pphStatus || 'TK/0 (Default)';

        // Grab existing bonus values from hidden inputs
        const thrVal = parseFloat(document.getElementById('thr_' + empId).value) || 0;
        const targetVal = parseFloat(document.getElementById('target_' + empId).value) || 0;
        const overtimeVal = parseFloat(document.getElementById('overtime_' + empId).value) || 0;

        document.getElementById('modal_thr').value = thrVal;
        document.getElementById('modal_target').value = targetVal;
        document.getElementById('modal_overtime').value = overtimeVal;

        // Reset all checkboxes visually first
        document.getElementById('cb_thr').checked = false;
        document.getElementById('cb_target').checked = false;
        document.getElementById('cb_overtime').checked = false;

        // Check if any exist and toggle them
        if(thrVal > 0) {
            document.getElementById('cb_thr').checked = true;
            toggleBonusInput('thr');
        } else {
            toggleBonusInput('thr');
        }

        if(targetVal > 0) {
            document.getElementById('cb_target').checked = true;
            toggleBonusInput('target');
        } else {
            toggleBonusInput('target');
        }

        if(overtimeVal > 0) {
            document.getElementById('cb_overtime').checked = true;
            toggleBonusInput('overtime');
        } else {
            toggleBonusInput('overtime');
        }

        calculateTotal();

        // Show Vanilla Modal
        modal.classList.remove('hidden');
        // Small delay for transition
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function saveBonuses() {
        const empId = document.getElementById('activeEmpId').value;
        document.getElementById('thr_' + empId).value = document.getElementById('modal_thr').value || 0;
        document.getElementById('target_' + empId).value = document.getElementById('modal_target').value || 0;
        document.getElementById('overtime_' + empId).value = document.getElementById('modal_overtime').value || 0;
        
        const checkbox = document.querySelector('.emp-checkbox[value="'+empId+'"]');
        if(checkbox && !checkbox.checked) {
            checkbox.checked = true;
            updateSelectedCount();
        }

        closeBonusModal();
    }

    function submitBatch() {
        const count = document.querySelectorAll('.emp-checkbox:checked').length;
        if (count === 0) {
            alert('Please select at least one employee.');
            return;
        }
        document.getElementById('payrollBatchForm').submit();
    }
</script>
@endpush
