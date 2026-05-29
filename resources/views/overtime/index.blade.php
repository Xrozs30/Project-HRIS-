@extends('layouts.admin')

@section('title', 'Overtime Request - HRIS')

@section('content')
<div class="flex items-center mb-8">
    <div class="bg-blue-50 text-blue-600 rounded-2xl w-12 h-12 flex items-center justify-center mr-4 shadow-sm">
        <i class="bi bi-briefcase-fill text-2xl"></i>
    </div>
    <div>
        <h3 class="font-bold text-2xl text-gray-800 mb-1">Overtime Request</h3>
        <p class="text-gray-500 text-sm mb-0">Submit overtime and see your history</p>
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

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-start shadow-sm" role="alert">
        <ul class="list-disc pl-5 m-0 mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
@endif

<!-- Submit Form Card -->
<div class="bg-white border-0 shadow-sm rounded-3xl mb-8 overflow-hidden">
    <div class="bg-gray-50/80 border-b border-gray-100 py-4 px-6">
        <h6 class="font-bold text-gray-800 text-lg m-0">Submit Overtime</h6>
    </div>
    <div class="p-6">
        <form action="{{ route('overtime.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="overtime_date" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="overtime_start" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required>
                    <div class="mt-1 text-xs text-gray-400">Format: 24 jam (00:00 - 23:59)</div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="overtime_finish" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required>
                    <div class="mt-1 text-xs text-gray-400">Format: 24 jam (00:00 - 23:59)</div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description (optional)</label>
                <textarea name="overtime_description" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow" rows="3" placeholder="Describe the task or reason..."></textarea>
            </div>



            <div class="text-center">
                <button type="submit" class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-sm transition-colors border-0 cursor-pointer text-base">
                    <i class="bi bi-send-fill mr-2"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- History Card -->
<div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden">
    <div class="bg-white border-b border-gray-100 py-4 px-6 flex items-center">
        <i class="bi bi-list-task text-blue-600 text-xl mr-3"></i>
        <h6 class="font-bold text-gray-800 text-lg m-0">My Overtime History</h6>
    </div>
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Start</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">End</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @forelse($overtimes as $ot)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-6 pr-4 font-bold text-sm text-gray-800">{{ date('Y-m-d', strtotime($ot->overtime_date)) }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600 font-medium">{{ \Carbon\Carbon::parse($ot->overtime_start)->format('H:i') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600 font-medium">{{ \Carbon\Carbon::parse($ot->overtime_finish)->format('H:i') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ Str::limit($ot->overtime_description ?: '-', 30) }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600 font-medium">{{ number_format($ot->overtime_duration, 1) }} hrs</td>
                            <td class="py-4 pr-6 pl-4 text-right">
                                @if($ot->overtime_status === 'pending')
                                    <span class="inline-flex items-center text-amber-700 bg-amber-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Pending</span>
                                @elseif($ot->overtime_status === 'approved')
                                    <span class="inline-flex items-center text-green-700 bg-green-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Approved</span>
                                @else
                                    <span class="inline-flex items-center text-red-700 bg-red-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Rejected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400 font-medium tracking-wide">No overtime history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
