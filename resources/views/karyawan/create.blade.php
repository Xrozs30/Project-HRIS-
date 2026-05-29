@extends('layouts.admin')

@section('title', 'Add Employee - HRIS')

@section('content')
    <div class="flex justify-start mb-6">
        <a href="{{ route('karyawan.index') }}" class="text-gray-500 hover:text-blue-600 font-bold no-underline flex items-center transition-colors">
            <i class="bi bi-arrow-left mr-2"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-5 mb-6 shadow-sm relative" role="alert">
            <h6 class="font-bold text-red-800 mb-2 flex items-center"><i class="bi bi-exclamation-triangle-fill mr-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0 text-sm pl-5 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border-0 shadow-sm rounded-2xl w-full max-w-4xl mx-auto overflow-hidden">
        <div class="p-6 md:p-10">
            <form action="{{ route('karyawan.store') }}" method="POST">
                @csrf
                
                <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-person-lines-fill mr-3 text-blue-500"></i> Personal Details</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                    {{-- Employee ID (auto-generated, read only) --}}
                    <div class="md:col-span-3">
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Employee ID</label>
                        <input type="text" value="{{ $newNik }}" class="w-full bg-gray-100 border border-gray-200 text-gray-600 rounded-xl px-4 py-3 cursor-not-allowed" readonly>
                        <p class="text-[10px] text-gray-400 mt-1">Auto-generated (ST0001)</p>
                    </div>
                    {{-- NIK KTP --}}
                    <div class="md:col-span-5">
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">NIK (No. Induk Kependudukan)</label>
                        <input type="text" name="employee_nik" value="{{ old('employee_nik') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" 
                               placeholder="3173040404040010" maxlength="16" required>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Role</label>
                        <select name="role" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors cursor-pointer" {{ auth()->user()->employee_role !== 'owner' ? 'disabled' : '' }}>
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            @if(auth()->user()->employee_role === 'owner')
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR</option>
                            @endif
                        </select>
                        @if(auth()->user()->employee_role !== 'owner')
                        <input type="hidden" name="role" value="employee">
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" name="employee_name" value="{{ old('employee_name') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="employee_email" value="{{ old('employee_email') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Phone</label>
                        <input type="text" name="employee_phone" value="{{ old('employee_phone') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Birth Date</label>
                        <input type="date" name="employee_birth_date" value="{{ old('employee_birth_date') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Gender</label>
                        <select name="employee_gender" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                            <option value="">Select Gender...</option>
                            <option value="male" {{ old('employee_gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('employee_gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Address</label>
                        <input type="text" name="employee_address" value="{{ old('employee_address') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                    </div>
                </div>
                
                <hr class="my-10 border-gray-100">

                <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-bank mr-3 text-cyan-500"></i> Bank & Insurance Details</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Bank Name</label>
                        <input type="text" name="employee_bank_name" value="{{ old('employee_bank_name') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" placeholder="e.g. BCA, BNI">
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Account Number</label>
                        <input type="text" name="employee_bank_number" value="{{ old('employee_bank_number') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">BPJS Number</label>
                        <input type="text" name="employee_bpjs_number" value="{{ old('employee_bpjs_number') }}" 
                               class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors">
                    </div>
                </div>

                <hr class="my-10 border-gray-100">

                <h5 class="font-extrabold text-xl mb-6 text-gray-800 flex items-center"><i class="bi bi-briefcase-fill mr-3 text-green-500"></i> Employment & Payroll</h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Position</label>
                        <select name="position_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors cursor-pointer" required>
                            <option value="" disabled {{ old('position_id') ? '' : 'selected' }}>Select Position...</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->position_id }}" {{ old('position_id') == $position->position_id ? 'selected' : '' }}>{{ ucwords($position->position_type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Basic Salary (Rp)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-gray-200 bg-gray-100 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="employee_basic_salary" value="{{ old('employee_basic_salary', 0) }}" 
                                   class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-r-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">PPh 21 Status</label>
                    <select name="tax_id" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required>
                        <option value="" disabled selected>Select Status...</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->tax_id }}" {{ old('tax_id') == $tax->tax_id ? 'selected' : '' }}>{{ $tax->tax_status }} ({{ $tax->tax_type }} - {{ $tax->tax_amount * 100 }}%)</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-10">
                    <label class="block font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="employee_password" 
                           class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-colors" required autocomplete="new-password">
                </div>

                <div class="mt-8 flex flex-col items-center">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition-colors shadow-sm cursor-pointer border-0">
                        <i class="bi bi-save mr-2"></i> Save New Employee
                    </button>
                    <p class="text-center text-gray-400 text-sm mt-4">Employee will be required to enroll their face upon first login.</p>
                </div>
            </form>
        </div>
    </div>
@endsection
