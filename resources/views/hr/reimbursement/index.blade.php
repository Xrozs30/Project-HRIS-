@extends('layouts.admin')

@section('title', 'Reimbursement Requests - HRIS')
@section('header_title', 'Reimbursement Requests')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
    <div class="flex items-center">
        <div class="bg-amber-50 text-amber-500 rounded-2xl w-12 h-12 flex items-center justify-center mr-4 shadow-sm">
            <i class="bi bi-receipt text-2xl"></i>
        </div>
        <div>
            <h2 class="font-bold text-2xl text-gray-800 mb-1">{{ auth()->user()->employee_role === 'owner' ? 'HR Approved Reimbursements' : 'Employee Reimbursement Requests' }}</h2>
            <p class="text-gray-500 text-sm mb-0">Review, approve, or reject accumulated monthly reimbursement claims</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-center shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="bi bi-check-circle-fill mr-2"></i> {{ session('success') }}
        </div>
        <button type="button" class="text-green-700 hover:text-green-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
@endif

<!-- Tabs Navigation -->
<div class="flex space-x-2 mb-6">
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-blue-600 text-white shadow-sm flex items-center" data-target="reimburse-pending">
        <i class="bi bi-hourglass-split mr-2"></i> Pending
        @if($pendingReimbursements->count() > 0)
            <span class="bg-red-500 text-white text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $pendingReimbursements->count() }}</span>
        @endif
    </button>
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 flex items-center" data-target="reimburse-history">
        <i class="bi bi-clock-history mr-2"></i> History
        @if($historyReimbursements->count() > 0)
            <span class="bg-gray-200 text-gray-700 text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $historyReimbursements->count() }}</span>
        @endif
    </button>
</div>

<!-- Tabs Content -->
<div class="tab-content">
    <!-- Pending Tab -->
    <div id="reimburse-pending" class="tab-panel block">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider w-16">#</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-center">Requests</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($pendingReimbursements as $item)
                                @php
                                    $monthName = date("F", mktime(0, 0, 0, $item->month, 1));
                                    $statusText = auth()->user()->employee_role === 'owner' ? 'HR Approved' : 'Pending';
                                    $statusColorClass = auth()->user()->employee_role === 'owner' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 pl-6 pr-4 text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                    <td class="py-4 px-4 font-bold text-sm text-gray-800">{{ $item->employee->employee_name ?? 'Unknown' }}</td>
                                    <td class="py-4 px-4 text-sm font-medium text-gray-600">{{ $monthName }} {{ $item->year }}</td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-gray-100 text-gray-600 font-bold px-3 py-1 rounded-lg text-xs">{{ $item->total_requests }} Items</span>
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-gray-800">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center {{ $statusColorClass }} font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">{{ $statusText }}</span>
                                    </td>
                                    <td class="py-4 pr-6 pl-4 text-right">
                                        <button class="view-details-btn inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-xl text-xs shadow-sm transition-colors border-0 cursor-pointer" 
                                            data-user="{{ $item->employee_id }}" 
                                            data-month="{{ $item->month }}" 
                                            data-year="{{ $item->year }}"
                                            data-name="{{ $item->employee->employee_name ?? 'Unknown' }}"
                                            data-status="{{ auth()->user()->employee_role === 'owner' ? 'hr_approved' : 'pending' }}">
                                            <i class="bi bi-ui-checks-grid mr-1.5"></i> Review Month
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-16 text-gray-400 font-medium tracking-wide">
                                        <i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>
                                        No pending reimbursement requests.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div id="reimburse-history" class="tab-panel hidden">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider w-16">#</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-center">Requests</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($historyReimbursements as $item)
                                @php
                                    $monthName = date("F", mktime(0, 0, 0, $item->month, 1));
                                    $statusText = 'Unknown';
                                    $statusColorClass = 'bg-gray-50 text-gray-600';
                                    if ($item->reimburse_status === 'hr_approved') {
                                        $statusText = 'HR Approved';
                                        $statusColorClass = 'bg-blue-50 text-blue-700';
                                    } elseif ($item->reimburse_status === 'approved') {
                                        $statusText = 'Approved';
                                        $statusColorClass = 'bg-green-50 text-green-700';
                                    } elseif ($item->reimburse_status === 'rejected') {
                                        $statusText = 'Rejected';
                                        $statusColorClass = 'bg-red-50 text-red-700';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 pl-6 pr-4 text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                    <td class="py-4 px-4 font-bold text-sm text-gray-800">{{ $item->employee->employee_name ?? 'Unknown' }}</td>
                                    <td class="py-4 px-4 text-sm font-medium text-gray-600">{{ $monthName }} {{ $item->year }}</td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-gray-100 text-gray-600 font-bold px-3 py-1 rounded-lg text-xs">{{ $item->total_requests }} Items</span>
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-gray-800">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center {{ $statusColorClass }} font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">{{ $statusText }}</span>
                                    </td>
                                    <td class="py-4 pr-6 pl-4 text-right">
                                        <button class="view-details-btn inline-flex items-center justify-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl text-xs shadow-sm transition-colors border-0 cursor-pointer" 
                                            data-user="{{ $item->employee_id }}" 
                                            data-month="{{ $item->month }}" 
                                            data-year="{{ $item->year }}"
                                            data-name="{{ $item->employee->employee_name ?? 'Unknown' }}"
                                            data-status="{{ $item->reimburse_status }}">
                                            <i class="bi bi-eye-fill mr-1.5"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-16 text-gray-400 font-medium tracking-wide">
                                        <i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>
                                        No history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Overlay Modal -->
<div id="tw-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="tw-reimbursementModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl flex flex-col transform scale-95 transition-transform duration-300 my-8" id="tw-reimbursementModalContent" style="max-height: 90vh;">
        
        <!-- Modal Header -->
        <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10 rounded-t-3xl">
            <h5 class="font-bold text-xl text-gray-800 m-0 flex items-center">
                <i class="bi bi-receipt text-amber-500 mr-3 text-2xl"></i> Monthly Reimbursement Detail
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 h-8 w-8 rounded-full border-0 cursor-pointer flex items-center justify-center transition-colors" onclick="closeReimbursementModal()">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        
        <!-- Modal Body (Scrollable) -->
        <div class="p-8 overflow-y-auto">
            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">Employee</div>
                    <div class="font-bold text-xl text-gray-800" id="detail-name">—</div>
                    <div class="text-sm text-gray-500 mt-2 font-medium">Period: <span id="detail-period" class="font-bold text-gray-800"></span></div>
                </div>
            </div>

            <!-- Items List -->
            <h6 class="font-bold text-gray-800 mb-4 text-lg">Submitted Receipts</h6>
            <div class="border border-gray-200 rounded-2xl overflow-hidden mb-8">
                <div>
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date Submitted</th>
                                <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider w-64">Description</th>
                                <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Attachment</th>
                            </tr>
                        </thead>
                        <tbody id="detail-items-list" class="divide-y divide-gray-100">
                            <tr><td colspan="4" class="text-center py-8 text-gray-400 font-medium">Loading...</td></tr>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="1" class="py-4 px-4 text-right font-bold text-gray-600 text-sm">Total Claimed:</td>
                                <td colspan="3" class="py-4 px-4 text-green-600 font-bold text-lg" id="detail-total-amount">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Action Forms -->
            <div class="bg-white pt-6 border-t border-gray-100" id="modal-action-section">
                <h6 class="font-bold text-gray-800 mb-6 text-lg">Take Action for Entire Month</h6>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Approve Form -->
                    <div class="bg-green-50/50 rounded-2xl p-6 border border-green-100">
                        <h6 class="font-bold text-green-800 mb-4 flex items-center"><i class="bi bi-check-circle-fill mr-2"></i> Approve Requests</h6>
                        <form id="approve-form" method="POST" action="{{ route('hr.reimbursement.approveBatch') }}">
                            @csrf
                            <input type="hidden" name="employee_id" id="approve_user_id">
                            <input type="hidden" name="month" id="approve_month">
                            <input type="hidden" name="year" id="approve_year">
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-green-700 uppercase tracking-wider mb-2">Approver Notes (Optional)</label>
                                <textarea name="notes" class="w-full bg-white border border-green-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 font-medium transition-shadow" rows="2" placeholder="Add a note..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition-colors border-0 cursor-pointer flex justify-center items-center text-sm" onclick="return confirm('Are you sure you want to approve this month\'s claims?')">
                                <i class="bi bi-check-circle-fill mr-2"></i> Approve All
                            </button>
                        </form>
                    </div>
                    
                    <!-- Reject Form -->
                    <div class="bg-red-50/50 rounded-2xl p-6 border border-red-100">
                        <h6 class="font-bold text-red-800 mb-4 flex items-center"><i class="bi bi-x-circle-fill mr-2"></i> Reject Requests</h6>
                        <form id="reject-form" method="POST" action="{{ route('hr.reimbursement.rejectBatch') }}">
                            @csrf
                            <input type="hidden" name="employee_id" id="reject_user_id">
                            <input type="hidden" name="month" id="reject_month">
                            <input type="hidden" name="year" id="reject_year">
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-red-700 uppercase tracking-wider mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                                <textarea name="notes" class="w-full bg-white border border-red-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500 font-medium transition-shadow" rows="2" placeholder="State the reason..." required></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 px-6 rounded-xl shadow-sm transition-colors border-0 cursor-pointer flex justify-center items-center text-sm" onclick="return confirm('Are you sure you want to reject this month\'s claims?')">
                                <i class="bi bi-x-circle-fill mr-2"></i> Reject All
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Vanilla JS Modal Logic
    const backdrop = document.getElementById('tw-backdrop');
    const modal = document.getElementById('tw-reimbursementModal');
    const modalContent = document.getElementById('tw-reimbursementModalContent');

    function openReimbursementModal() {
        // Stop background scrolling
        document.body.style.overflow = 'hidden';
        
        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeReimbursementModal() {
        // Restore background scrolling
        document.body.style.overflow = '';

        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        
        setTimeout(() => { 
            modal.classList.add('hidden');
            backdrop.classList.add('hidden');
        }, 300);
    }

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeReimbursementModal();
        }
    });

    // Handle AJAX and Data Population
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.view-details-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const userId = this.dataset.user;
                const month = this.dataset.month;
                const year = this.dataset.year;
                const name = this.dataset.name;
                const status = this.dataset.status;
                
                // Set basic info
                document.getElementById('detail-name').textContent = name;
                const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                document.getElementById('detail-period').textContent = `${monthNames[parseInt(month)]} ${year}`;
                
                // Set form inputs
                document.getElementById('approve_user_id').value = userId;
                document.getElementById('approve_month').value = month;
                document.getElementById('approve_year').value = year;
                
                document.getElementById('reject_user_id').value = userId;
                document.getElementById('reject_month').value = month;
                document.getElementById('reject_year').value = year;

                // Show/hide Action Forms based on status
                const userRole = "{{ auth()->user()->employee_role }}";
                const actionSection = document.getElementById('modal-action-section');
                if ((userRole === 'hr' && status === 'pending') || (userRole === 'owner' && status === 'hr_approved')) {
                    actionSection.classList.remove('hidden');
                } else {
                    actionSection.classList.add('hidden');
                }

                // Show modal first with loading state
                const tbody = document.getElementById('detail-items-list');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400 font-medium"><div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-amber-500 mb-2"></div><br>Loading details...</td></tr>';
                document.getElementById('detail-total-amount').textContent = 'Rp 0';
                
                openReimbursementModal();

                try {
                    const res = await fetch(`/hr/reimbursement/batch?employee_id=${userId}&month=${month}&year=${year}&status=${status}`);
                    const data = await res.json();

                    tbody.innerHTML = '';
                    let totalAmount = 0;

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400 font-medium">No items found.</td></tr>';
                    } else {
                        data.forEach(item => {
                            totalAmount += parseFloat(item.reimburse_total);
                            
                            const dateStr = new Date(item.reimburse_date).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'});
                            const amountStr = 'Rp ' + Number(item.reimburse_total).toLocaleString('id-ID');
                            
                            let fileLink = '<span class="text-xs text-gray-400 font-medium italic">No File</span>';
                            if (item.reimburse_proof) {
                                fileLink = `<a href="/storage/${item.reimburse_proof}" target="_blank" class="inline-flex items-center justify-center p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors font-bold text-[10px]"><i class="bi bi-file-earmark-arrow-down mr-1.5 text-sm"></i> View</a>`;
                            }
                            
                            const tr = document.createElement('tr');
                            tr.className = "hover:bg-gray-50/50 transition-colors";
                            tr.innerHTML = `
                                <td class="py-4 px-4 text-sm text-gray-700 font-medium">${dateStr}</td>
                                <td class="py-4 px-4 text-sm text-gray-800 font-bold">${amountStr}</td>
                                <td class="py-4 px-4 text-sm text-gray-600 whitespace-normal break-words" style="max-width: 250px;">${item.reimburse_description}</td>
                                <td class="py-4 px-4">${fileLink}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                    
                    document.getElementById('detail-total-amount').textContent = 'Rp ' + Number(totalAmount).toLocaleString('id-ID');

                } catch (err) {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-red-500 font-medium">Failed to load reimbursement details.</td></tr>';
                }
            });
        });

        // Tab switching logic
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active classes from all buttons
                tabBtns.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-50');
                });

                // Hide all panels
                tabPanels.forEach(p => {
                    p.classList.remove('block');
                    p.classList.add('hidden');
                });

                // Add active classes to clicked button
                btn.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-50');
                btn.classList.add('bg-blue-600', 'text-white');

                // Show target panel
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.remove('hidden');
                document.getElementById(targetId).classList.add('block');
            });
        });
    });
</script>
@endpush
