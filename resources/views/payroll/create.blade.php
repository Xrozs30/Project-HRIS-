@extends('layouts.admin')

@section('title', 'Create Payroll - HRIS')

@section('content')
    <div class="flex items-center mb-6">
        <a href="{{ route('payroll.index') }}" class="text-gray-500 hover:text-gray-700 font-bold flex items-center transition-colors no-underline">
            <i class="bi bi-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="bg-white border-0 shadow-sm rounded-3xl overflow-hidden max-w-3xl mx-auto">
        <div class="p-8 md:p-10">
            <form action="{{ route('payroll.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                    <div class="md:col-span-6">
                        <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Employee</label>
                        <select name="employee_id" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" required>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->payroll_id }}">{{ $emp->employee_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Month</label>
                        <select name="month" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" required>
                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                <option value="{{ $m }}" {{ date('F') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Year</label>
                        <input type="number" name="year" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" value="{{ date('Y') }}" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Basic Salary</label>
                    <input type="number" name="basic_salary" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Allowances</label>
                        <input type="number" name="allowances" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" value="0">
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Deductions</label>
                        <input type="number" name="deductions" class="w-full bg-gray-50 border border-gray-100 text-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all" value="0">
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-md transition-all border-0 cursor-pointer text-lg">Save Payroll</button>
                </div>

            </form>
        </div>
    </div>
@endsection
