@extends('layouts.admin')

@section('title', 'Leave & Permission - HRIS')

@section('content')
    <div class="flex justify-end mb-6">
        <a href="{{ route('leave.create') }}" class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 font-bold px-6 py-2.5 rounded-xl shadow-sm transition-colors no-underline">
            <i class="bi bi-plus-lg mr-2"></i> Apply Leave
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-center" role="alert">
            <div>{{ session('success') }}</div>
            <button type="button" class="text-green-700 hover:text-green-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden mb-6">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-gray-50/80 border-b border-gray-100">
                        <tr>
                            <th class="py-4 pl-6 pr-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="py-4 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-4 pr-6 pl-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/50">
                        @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-6 pr-4">
                                @if($req->leave_type == 'cuti')
                                    <span class="bg-blue-50 text-blue-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">LEAVE</span>
                                @elseif($req->leave_type == 'ijin')
                                    <span class="bg-amber-50 text-amber-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">PERMISSION</span>
                                @else
                                    <span class="bg-red-50 text-red-600 font-bold px-3 py-1.5 rounded-full text-[10px] tracking-wide">SICK</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-sm text-gray-800">{{ $req->leave_start_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">to {{ $req->leave_end_date->format('d M Y') }}</div>
                            </td>
                            <td class="py-4 px-4 font-bold text-sm text-gray-800">{{ $req->leave_duration }} Days</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($req->leave_reason, 30) }}</td>
                            <td class="py-4 px-4 text-sm">
                                @if($req->leave_status == 'approved')
                                    <span class="inline-flex items-center text-green-700 bg-green-50 font-bold px-2.5 py-1 rounded-md text-xs">Approved</span>
                                @elseif($req->leave_status == 'rejected')
                                    <span class="inline-flex items-center text-red-700 bg-red-50 font-bold px-2.5 py-1 rounded-md text-xs">Rejected</span>
                                @else
                                    <span class="inline-flex items-center text-gray-600 bg-gray-100 font-bold px-2.5 py-1 rounded-md text-xs">Pending</span>
                                @endif
                            </td>
                            <td class="py-4 pr-6 pl-4 text-right">
                                @if($req->leave_status == 'pending')
                                <button class="bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold py-1.5 px-4 rounded-xl text-xs shadow-sm transition-colors cursor-pointer">Cancel</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400 font-medium tracking-wide">No leave history.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
