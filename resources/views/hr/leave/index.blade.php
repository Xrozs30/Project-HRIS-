@extends('layouts.admin')

@section('title', 'Leave & Permission - HRIS')
@section('header_title', 'Leave & Permission')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
    <div>
        <h2 class="font-bold text-2xl text-gray-800 mb-1">Leave & Permission</h2>
        <p class="text-gray-500 text-sm mb-0">Review and manage {{ auth()->user()->employee_role === 'owner' ? 'HR' : 'employee' }} leave applications</p>
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
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-blue-600 text-white shadow-sm flex items-center" data-target="leave-pending">
        <i class="bi bi-hourglass-split mr-2"></i> Pending
        @if($pendingRequests->count() > 0)
            <span class="bg-red-500 text-white text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        @endif
    </button>
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 flex items-center" data-target="leave-history">
        <i class="bi bi-clock-history mr-2"></i> History
        <span class="bg-gray-200 text-gray-700 text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $historyRequests->count() }}</span>
    </button>
</div>

<!-- Tabs Content -->
<div class="tab-content">
    <!-- Pending Tab -->
    <div id="leave-pending" class="tab-panel block">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse($pendingRequests as $req)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-4 font-bold text-sm text-gray-800">{{ $req->employee->employee_name }}</td>
                                <td class="py-4 px-4">
                                    @if($req->leave_type == 'cuti') <span class="bg-blue-50 text-blue-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">LEAVE</span>
                                    @elseif($req->leave_type == 'ijin') <span class="bg-amber-50 text-amber-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">PERMISSION</span>
                                    @else <span class="bg-red-50 text-red-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">SICK</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-sm text-gray-800">{{ $req->leave_start_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">to {{ $req->leave_end_date->format('d M Y') }}</div>
                                </td>
                                <td class="py-4 px-4 font-bold text-sm text-gray-800">{{ $req->leave_duration }} Days</td>
                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ Str::limit($req->leave_reason, 30) }}
                                    @if($req->leave_sick_proof)
                                        <br><a href="{{ asset('storage/'.$req->leave_sick_proof) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 mt-1 inline-block no-underline"><i class="bi bi-file-earmark-text mr-1"></i>View Proof</a>
                                    @endif
                                </td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    <form action="{{ route('hr.leave.approve', $req->leave_id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-1.5 px-3 rounded-lg text-xs shadow-sm transition-colors border-0 cursor-pointer">Approve</button>
                                    </form>
                                    <button class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold py-1.5 px-3 rounded-lg text-xs shadow-sm transition-colors cursor-pointer ml-1" onclick="openRejectModal('{{ $req->leave_id }}')">Reject</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-16 text-gray-400 font-medium tracking-wide"><i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>No pending applications.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100/50 bg-white">
                    {{ $pendingRequests->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div id="leave-history" class="tab-panel hidden">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse($historyRequests as $req)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-4 font-bold text-sm text-gray-800">{{ $req->employee->employee_name }}</td>
                                <td class="py-4 px-4">
                                    @if($req->leave_type == 'cuti') <span class="bg-blue-50 text-blue-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">LEAVE</span>
                                    @elseif($req->leave_type == 'ijin') <span class="bg-amber-50 text-amber-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">PERMISSION</span>
                                    @else <span class="bg-red-50 text-red-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">SICK</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-sm text-gray-800">{{ $req->leave_start_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">to {{ $req->leave_end_date->format('d M Y') }}</div>
                                </td>
                                <td class="py-4 px-4 font-bold text-sm text-gray-800">{{ $req->leave_duration }} Days</td>
                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ Str::limit($req->leave_reason, 30) }}
                                    @if($req->leave_sick_proof)
                                        <br><a href="{{ asset('storage/'.$req->leave_sick_proof) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 mt-1 inline-block no-underline"><i class="bi bi-file-earmark-text mr-1"></i>View Proof</a>
                                    @endif
                                </td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    @if($req->leave_status == 'approved')
                                        <span class="inline-flex items-center text-green-700 bg-green-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide"><i class="bi bi-check-circle mr-1"></i>Approved</span>
                                    @else
                                        <span class="inline-flex items-center text-red-700 bg-red-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide" title="{{ $req->leave_rejection_reason }}"><i class="bi bi-x-circle mr-1"></i>Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-16 text-gray-400 font-medium tracking-wide"><i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>No processed requests yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100/50 bg-white">
                    {{ $historyRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Reject Modal -->
<div id="tw-backdrop" class="fixed inset-0 bg-gray-900/50 z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="tw-rejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="tw-rejectModalContent">
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h5 class="font-bold text-lg text-gray-800 m-0">Reject Application</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 bg-transparent border-0 cursor-pointer text-xl" onclick="closeRejectModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rejection Reason</label>
                <textarea name="leave_rejection_reason" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50">
                <button type="button" class="bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-2.5 px-5 rounded-xl transition-colors cursor-pointer" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors border-0 cursor-pointer">Reject</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tab switching logic
    document.addEventListener('DOMContentLoaded', () => {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active classes from all buttons
                tabBtns.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-50');
                    if(b.querySelector('.bg-red-500')) {
                        b.querySelector('.bg-red-500').classList.replace('text-blue-600', 'text-white'); // Fix badge inner colors if needed
                    }
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

    // Vanilla JS Modal logic for Reject
    const backdrop = document.getElementById('tw-backdrop');
    const modal = document.getElementById('tw-rejectModal');
    const modalContent = document.getElementById('tw-rejectModalContent');
    const rejectForm = document.getElementById('rejectForm');

    function openRejectModal(requestId) {
        // Set form action dynamic path
        const basePath = "{{ url('/hr/leave/') }}";
        rejectForm.action = `${basePath}/${requestId}/reject`;

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

    function closeRejectModal() {
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
</script>
@endpush
