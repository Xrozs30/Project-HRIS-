@extends('layouts.admin')

@section('title', 'Dashboard - HRIS')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Card 1 -->
    <div class="bg-white border-0 shadow-sm rounded-2xl p-6 tooltip-hover">
        <div class="flex justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-50 text-yellow-500 rounded-xl flex items-center justify-center text-2xl"><i class="bi bi-people"></i></div>
        </div>
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Employees</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalEmployees }}</div>
    </div>
    
    <!-- Card 2 -->
    <div class="bg-white border-0 shadow-sm rounded-2xl p-6">
        <div class="flex justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-2xl"><i class="bi bi-fingerprint"></i></div>
            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold h-fit">{{ $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100) : 0 }}% Present</span>
        </div>
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider">Present Today</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">{{ $presentToday }}</div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white border-0 shadow-sm rounded-2xl p-6">
        <div class="flex justify-between mb-4">
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-xl flex items-center justify-center text-2xl"><i class="bi bi-airplane-fill"></i></div>
        </div>
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider">On Leave</div>
        <div class="text-3xl font-extrabold text-gray-800 mt-1">{{ $onLeave }} <span class="text-sm text-gray-400 ml-1 font-medium">Ppl</span></div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white border-0 shadow-sm rounded-2xl p-6">
        <div class="flex justify-between mb-4">
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-2xl"><i class="bi bi-wallet2"></i></div>
        </div>
        <div class="text-gray-400 text-xs font-bold uppercase tracking-wider">Estimasi Payroll</div>
        <div class="text-2xl font-extrabold text-gray-800 mt-2 tracking-tight">Rp {{ number_format($estimasiPayroll, 0, ',', '.') }}</div>
    </div>
</div>

<div class="mt-8">
    <div class="bg-white border-0 shadow-sm rounded-2xl p-6">
        <h5 class="font-bold text-lg text-gray-800 mb-6">Latest Attendance Today</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider rounded-l-xl">Employee</th>
                        <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Division</th>
                        <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Time In</th>
                        <th class="py-3 px-4 font-bold text-[11px] text-gray-500 uppercase tracking-wider rounded-r-xl">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @forelse ($latestAttendances as $attendance)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($attendance->employee->employee_name) }}&background=random" class="rounded-full mr-3 w-9 h-9 shadow-sm">
                                <div>
                                    <div class="font-bold text-sm text-gray-800">{{ $attendance->employee->employee_name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $attendance->employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $attendance->employee->position->position_type ?? '-' }}</td>
                        <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $attendance->presence_time_in }} WIB</td>
                        <td class="py-3 px-4">
                            @if(strtolower($attendance->presence_status) == 'tepat waktu' || strtolower($attendance->presence_status) == 'on time' || strtolower($attendance->presence_status) == 'hadir')
                                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold inline-block">{{ $attendance->presence_status }}</span>
                            @elseif(strtolower($attendance->presence_status) == 'terlambat' || strtolower($attendance->presence_status) == 'late')
                                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold inline-block">{{ $attendance->presence_status }}</span>
                            @else
                                <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-full text-xs font-bold inline-block">{{ $attendance->presence_status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400 text-sm">No attendance records yet today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
