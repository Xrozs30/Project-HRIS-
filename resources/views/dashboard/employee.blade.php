@extends('layouts.admin')

@section('title', 'Employee Dashboard - HRIS')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="col-span-1 md:col-span-2">
        <div class="bg-gradient-to-br from-[#FFD700] to-[#FFA500] text-white rounded-2xl p-6 mb-6 shadow-sm border-0">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-2xl mb-1">{{ date('l, d F Y') }}</h2>
                    <p class="mb-0 text-white/75 text-sm">Don't forget to clock in/out today!</p>
                </div>
                <div class="text-right">
                    <h1 class="font-extrabold text-4xl md:text-5xl mb-0 tracking-tight" id="clock">{{ date('H:i') }}</h1>
                    <small class="uppercase tracking-widest text-xs font-bold opacity-90">WIB</small>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <a href="{{ route('presence.create') }}" class="block no-underline group h-full">
                    <div class="bg-white border-0 shadow-sm rounded-2xl p-6 text-center h-full transition-transform duration-200 group-hover:scale-[1.02]">
                        <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg mb-1">Attendance</h4>
                        <p class="text-gray-500 text-sm m-0">Clock In / Clock Out with Camera</p>
                    </div>
                </a>
            </div>
            <div>
                 <a href="{{ route('payroll.index') }}" class="block no-underline group h-full">
                    <div class="bg-white border-0 shadow-sm rounded-2xl p-6 text-center h-full transition-transform duration-200 group-hover:scale-[1.02]">
                        <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg mb-1">My Payslips</h4>
                         <p class="text-gray-500 text-sm m-0">View your salary history</p>
                    </div>
                </a>
            </div>
            <div class="sm:col-span-2">
                 <a href="{{ route('leave.index') }}" class="block no-underline group h-full">
                    <div class="bg-white border-0 shadow-sm rounded-2xl p-6 text-center h-full transition-transform duration-200 group-hover:scale-[1.02]">
                        <div class="w-20 h-20 bg-yellow-50 text-yellow-500 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg mb-1">Leave Request</h4>
                         <p class="text-gray-500 text-sm m-0">Apply for leave or permission</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-span-1">
        <div class="bg-white border-0 shadow-sm rounded-2xl p-6 h-full">
            <h5 class="font-bold text-gray-800 text-lg mb-6">Attendance History</h5>
            @php
                $history = \App\Models\Presence::where('employee_id', auth()->id())
                    ->orderBy('presence_date', 'desc')
                    ->take(5)
                    ->get();
            @endphp

            @if($history->count() > 0)
                <div class="flex flex-col gap-4">
                    @foreach ($history as $h)
                    <div class="flex items-start">
                        <div class="mr-4 mt-1">
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500">
                                <i class="bi bi-calendar-check mt-1"></i>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ date('d M Y', strtotime($h->presence_date)) }}</div>
                            <div class="text-sm">
                                <span class="text-green-500 font-semibold">In: {{ $h->presence_time_in }}</span>
                                @if($h->presence_time_out)
                                    <span class="text-red-500 font-semibold ml-2">Out: {{ $h->presence_time_out }}</span>
                                @else
                                    <span class="text-yellow-500 font-semibold ml-2">Not yet out</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-400 py-10">
                    <i class="bi bi-calendar-x text-5xl mb-3 block opacity-50"></i>
                    <p class="text-sm">No attendance history found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const clockTarget = document.getElementById('clock');
        if (clockTarget) clockTarget.textContent = `${hours}:${minutes}`;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush
@endsection
