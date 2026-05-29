@extends('layouts.admin')

@section('title', 'Employees - HRIS')

@section('content')
    <div class="flex justify-end mb-6">
        <a href="{{ route('karyawan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white shadow-sm font-bold px-5 py-2.5 rounded-xl no-underline transition-colors flex items-center">
            <i class="bi bi-person-plus-fill mr-2"></i> Add Employee
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 mb-6 relative shadow-sm" role="alert">
            <span class="block sm:inline font-semibold">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white border-0 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider rounded-l-xl">Name</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Email & Contact</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="py-4 px-5 font-bold text-[11px] text-gray-500 uppercase tracking-wider text-right rounded-r-xl">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/50">
                    @if ($employees->count() > 0)
                        @foreach ($employees as $emp)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-5">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl mr-4 bg-gray-100 text-gray-600 font-bold flex items-center justify-center shadow-sm">{{ substr($emp->employee_name, 0, 2) }}</div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                        {{ $emp->employee_name }}
                                        @if($emp->employee_role === 'hr')
                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-[4px] text-[10px] font-bold uppercase">HR</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $emp->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <div class="text-sm font-semibold text-gray-800">{{ $emp->employee_email }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $emp->employee_phone ?? '-' }}</div>
                        </td>
                        <td class="py-4 px-5">
                            @if($emp->position)
                                <span class="bg-cyan-50 text-cyan-600 px-3 py-1.5 rounded-full text-xs font-bold uppercase inline-block">{{ $emp->position->position_type }}</span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            @if(auth()->user()->employee_role === 'owner' && $emp->employee_role === 'employee')
                                <span class="text-gray-400 text-xs italic">Read-only</span>
                            @else
                                <a href="{{ route('karyawan.edit', $emp->employee_id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 font-bold px-3 py-1.5 rounded-lg text-xs mr-2 transition-colors no-underline">Edit</a>
                                <form action="{{ route('karyawan.destroy', $emp->employee_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors border-0 cursor-pointer">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-400 font-bold">No data available.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
