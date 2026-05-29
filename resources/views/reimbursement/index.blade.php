@extends('layouts.admin')

@section('title', 'My Reimbursement Requests - HRIS')
@section('header_title', 'My Reimbursements')

@section('content')
<div class="flex items-center justify-between mb-8 flex-wrap gap-4">
    <div class="flex items-center">
        <div class="bg-amber-50 text-amber-500 rounded-2xl w-12 h-12 flex items-center justify-center mr-4 shadow-sm">
            <i class="bi bi-receipt text-2xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-2xl text-gray-800 mb-1">My Reimbursements</h3>
            <p class="text-gray-500 text-sm mb-0">Track the status of your submitted claims</p>
        </div>
    </div>
    <a href="{{ route('reimbursement.create') }}" class="inline-flex items-center text-white bg-amber-500 hover:bg-amber-600 font-bold px-6 py-3 rounded-xl shadow-sm transition-colors no-underline">
        <i class="bi bi-plus-lg mr-2"></i> Submit New
    </a>
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

<div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
    <div class="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider w-16">#</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider w-64">Description</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Attachment</th>
                        <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">HR Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @forelse ($reimbursements as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-6 pr-4 text-sm text-gray-400 font-medium">{{ $loop->iteration }}</td>
                            <td class="py-4 px-4 text-sm text-gray-700 font-bold">{{ \Carbon\Carbon::parse($item->presence_date)->format('d M Y') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-800 font-bold">Rp {{ number_format($item->reimburse_total, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600 whitespace-normal break-words" style="max-width: 250px;">{{ $item->reimburse_description }}</td>
                            <td class="py-4 px-4">
                                @if($item->reimburse_proof)
                                    <a href="{{ Storage::url($item->reimburse_proof) }}" target="_blank" class="inline-flex items-center justify-center p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Attachment">
                                        <i class="bi bi-file-earmark-arrow-down text-lg"></i>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 font-medium italic">No file</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if($item->presence_status == 'pending')
                                    <span class="inline-flex items-center text-amber-700 bg-amber-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Pending</span>
                                @elseif($item->presence_status == 'hr_approved')
                                    <span class="inline-flex items-center text-blue-700 bg-blue-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">HR Approved</span>
                                @elseif($item->presence_status == 'approved')
                                    <span class="inline-flex items-center text-green-700 bg-green-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Approved & Paid</span>
                                @else
                                    <span class="inline-flex items-center text-red-700 bg-red-50 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">Rejected</span>
                                @endif
                            </td>
                            <td class="py-4 pr-6 pl-4 text-xs text-gray-500 whitespace-normal break-words" style="max-width: 200px;">
                                {{ $item->reimburse_notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-gray-400 font-medium tracking-wide">
                                <i class="bi bi-inbox text-4xl block mb-3 text-gray-300"></i>
                                No reimbursement requests yet.<br>
                                <a href="{{ route('reimbursement.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-bold mt-3 no-underline">Submit your first request</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
