@extends('layouts.admin')

@section('title', 'Overtime - HRIS')
@section('header_title', 'Overtime')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
    <div>
        <h2 class="font-bold text-2xl text-gray-800 mb-1">Overtime</h2>
        <p class="text-gray-500 text-sm mb-0">Review and manage {{ auth()->user()->employee_role === 'owner' ? 'HR' : 'employee' }} overtime requests</p>
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
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-blue-600 text-white shadow-sm flex items-center" data-target="ot-pending">
        <i class="bi bi-hourglass-split mr-2"></i> Pending
        @if($pendingOvertimes->count() > 0)
            <span class="bg-red-500 text-white text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $pendingOvertimes->count() }}</span>
        @endif
    </button>
    <button class="tab-btn px-5 py-2.5 font-bold rounded-xl text-sm transition-colors bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 flex items-center" data-target="ot-history">
        <i class="bi bi-clock-history mr-2"></i> History
        <span class="bg-gray-200 text-gray-700 text-[10px] ml-2 px-2 py-0.5 rounded-full">{{ $historyOvertimes->count() }}</span>
    </button>
</div>

<!-- Tabs Content -->
<div class="tab-content">
    <!-- Pending Tab -->
    <div id="ot-pending" class="tab-panel block">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Actual Attd.</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse($pendingOvertimes as $ot)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-4">
                                    <div class="font-bold text-sm text-gray-800">{{ $ot->employee->employee_name ?? 'Unknown' }}</div>
                                    <div class="text-[11px] text-gray-500 tracking-wide mt-0.5">{{ $ot->employee->position->position_type ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4 text-sm font-bold text-gray-700">{{ date('d M Y', strtotime($ot->overtime_date)) }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600 font-medium">
                                    {{ \Carbon\Carbon::parse($ot->overtime_start)->format('H:i') }} – {{ \Carbon\Carbon::parse($ot->overtime_finish)->format('H:i') }}
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ot->overtime_duration }} hrs</div>
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    @if($ot->attendance)
                                        <span class="text-green-600 font-bold">{{ \Carbon\Carbon::parse($ot->attendance->presence_time_in)->format('H:i') }}</span>
                                        <span class="text-gray-400 mx-1">–</span>
                                        @if($ot->attendance->presence_time_out)
                                            <span class="text-green-600 font-bold">{{ \Carbon\Carbon::parse($ot->attendance->presence_time_out)->format('H:i') }}</span>
                                        @else
                                            <span class="text-amber-500 italic font-medium">No Clock-Out</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic font-medium">No Record</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($ot->overtime_description ?: '-', 30) }}</td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('hr.overtime.approve', $ot->overtime_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1.5 px-4 rounded-full text-xs shadow-sm transition-colors border-0 cursor-pointer flex items-center" onclick="return confirm('Approve this overtime?')">
                                                <i class="bi bi-check2 mr-1 text-sm"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('hr.overtime.reject', $ot->overtime_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold py-1.5 px-4 rounded-full text-xs shadow-sm transition-colors cursor-pointer flex items-center" onclick="return confirm('Reject this overtime?')">
                                                <i class="bi bi-x mr-1 text-sm"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-16 text-gray-400 font-medium tracking-wide"><i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>No pending overtime requests.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div id="ot-history" class="tab-panel hidden">
        <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-gray-50/80 border-b border-gray-100">
                            <tr>
                                <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Actual Attd.</th>
                                <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse($historyOvertimes as $ot)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 pl-6 pr-4">
                                    <div class="font-bold text-sm text-gray-800">{{ $ot->employee->employee_name ?? 'Unknown' }}</div>
                                    <div class="text-[11px] text-gray-500 tracking-wide mt-0.5">{{ $ot->employee->position->position_type ?? '' }}</div>
                                </td>
                                <td class="py-4 px-4 text-sm font-bold text-gray-700">{{ date('d M Y', strtotime($ot->overtime_date)) }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600 font-medium">
                                    {{ \Carbon\Carbon::parse($ot->overtime_start)->format('H:i') }} – {{ \Carbon\Carbon::parse($ot->overtime_finish)->format('H:i') }}
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $ot->overtime_duration }} hrs</div>
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    @if($ot->attendance)
                                        <span class="text-green-600 font-bold">{{ \Carbon\Carbon::parse($ot->attendance->presence_time_in)->format('H:i') }}</span>
                                        <span class="text-gray-400 mx-1">–</span>
                                        @if($ot->attendance->presence_time_out)
                                            <span class="text-green-600 font-bold">{{ \Carbon\Carbon::parse($ot->attendance->presence_time_out)->format('H:i') }}</span>
                                        @else
                                            <span class="text-amber-500 italic font-medium">No Clock-Out</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic font-medium">No Record</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($ot->overtime_description ?: '-', 30) }}</td>
                                <td class="py-4 pr-6 pl-4 text-right">
                                    @if($ot->overtime_status === 'approved')
                                        <span class="inline-flex items-center text-green-700 bg-green-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide"><i class="bi bi-check-circle mr-1"></i>Approved</span>
                                    @else
                                        <span class="inline-flex items-center text-red-700 bg-red-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide"><i class="bi bi-x-circle mr-1"></i>Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-16 text-gray-400 font-medium tracking-wide"><i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>No overtime history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
</script>
@endpush
