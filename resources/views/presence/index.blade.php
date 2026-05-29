@extends('layouts.admin')

@section('title', 'Attendance - HRIS')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-6 gap-4">
        <div>
            <h3 class="font-bold text-2xl text-gray-800 mb-1">Employee Attendance</h3>
            <p class="text-gray-500 text-sm mb-0">Manage and view daily attendance records.</p>
        </div>
        <form action="{{ url('/presence') }}" method="GET" class="flex gap-2">
            <input type="date" name="date" class="bg-white border border-gray-200 text-gray-600 font-bold rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" value="{{ $date ?? date('Y-m-d') }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white border-0 font-bold px-4 py-2 rounded-xl shadow-sm transition-colors cursor-pointer">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="bg-white border-0 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider rounded-l-xl">Employee</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Time In</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Photo In</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Time Out</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right rounded-r-xl">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @forelse ($attendance as $p)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-5">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl mr-4 bg-gray-100 text-gray-500 font-bold flex items-center justify-center shadow-sm">{{ substr($p->employee->employee_name, 0, 2) }}</div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800">{{ $p->employee->employee_name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $p->employee->employee_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5 font-bold text-sm text-gray-700">{{ $p->presence_time_in }}</td>
                        <td class="py-4 px-5">
                            <img src="{{ asset('storage/presence/'.$p->presence_photo_in) }}" class="rounded-lg shadow-sm w-12 h-12 object-cover border border-gray-100">
                        </td>
                        <td class="py-4 px-5 font-bold text-sm text-gray-700">{{ $p->presence_time_out ?? '-' }}</td>
                        <td class="py-4 px-5">
                            @if($p->presence_status == 'late')
                                <span class="bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-xs font-bold inline-block">Late</span>
                            @else
                                <span class="bg-green-50 text-green-600 px-3 py-1.5 rounded-full text-xs font-bold inline-block">On Time</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            @if($p->presence_lat && $p->presence_long)
                            <a href="https://maps.google.com/?q={{ $p->presence_lat }},{{ $p->presence_long }}" target="_blank" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors no-underline inline-flex items-center">
                                <i class="bi bi-geo-alt-fill mr-1"></i> Maps
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-gray-400">
                            <i class="bi bi-inbox text-5xl block mb-4 text-gray-300"></i>
                            <div class="font-bold text-lg text-gray-600 mb-1">No Records Found</div>
                            <div class="text-sm">There isn't any attendance data recorded for {{ \Carbon\Carbon::parse($date ?? date('Y-m-d'))->format('l, d F Y') }}.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
