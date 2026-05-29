@extends('layouts.admin')

@section('title', 'Apply Leave - HRIS')

@section('content')
    <div class="flex justify-start mb-6">
        <a href="{{ route('leave.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-800 font-bold text-sm transition-colors no-underline">
            <i class="bi bi-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="bg-white border-0 shadow-sm rounded-3xl max-w-3xl">
        <div class="p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex justify-between items-start" role="alert">
                    <ul class="list-disc pl-5 m-0 mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.style.display='none'">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            @endif
            <form action="{{ route('leave.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Leave Type</label>
                    <div class="relative">
                        <select name="leave_type" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium appearance-none transition-shadow cursor-pointer" required>
                            <option value="cuti">Annual Leave</option>
                            <option value="ijin">Permission (Personal)</option>
                            <option value="sakit">Sick Leave</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                        <input type="date" name="leave_start_date" min="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                        <input type="date" name="leave_end_date" min="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Reason</label>
                    <textarea name="leave_reason" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow" rows="4" placeholder="Explain your reason..." required></textarea>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Supporting Document (Optional)</label>
                    <input type="file" name="leave_sick_proof" class="w-full bg-gray-50 border border-gray-200 text-gray-600 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <div class="mt-2 text-xs text-gray-400 font-medium">Example: Doctor's Note (for sick leave).</div>
                </div>

                <div class="grid">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-sm transition-colors border-0 cursor-pointer text-lg">Submit Application</button>
                </div>

            </form>
        </div>
    </div>
@endsection
