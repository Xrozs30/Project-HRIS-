@extends('layouts.admin')

@section('title', 'Submit Reimbursement - HRIS')
@section('header_title', 'New Reimbursement Request')

@section('content')
<div class="flex items-center mb-8">
    <div class="bg-amber-50 text-amber-500 rounded-2xl w-12 h-12 flex items-center justify-center mr-4 shadow-sm">
        <i class="bi bi-receipt text-2xl"></i>
    </div>
    <div>
        <h3 class="font-bold text-2xl text-gray-800 mb-1">Reimbursement Form</h3>
        <p class="text-gray-500 text-sm mb-0">Fill in the details of your claim below</p>
    </div>
</div>

<div class="bg-white border-0 shadow-sm rounded-3xl w-full mb-8">
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

        <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Date of Expense <span class="text-red-500">*</span></label>
                <input type="date" name="reimburse_date" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow cursor-pointer" required value="{{ old('date', date('Y-m-d')) }}">
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Amount Claimed (Rp) <span class="text-red-500">*</span></label>
                <div class="relative flex items-stretch w-full">
                    <span class="flex items-center whitespace-nowrap px-4 bg-gray-100 border border-gray-200 border-r-0 rounded-l-xl text-gray-600 font-bold">Rp</span>
                    <input type="number" name="reimburse_total" class="w-full bg-gray-50 border border-gray-200 focus:border-blue-500 text-gray-800 rounded-r-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow" required min="0" step="1000" value="{{ old('amount') }}" placeholder="0">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description / Details <span class="text-red-500">*</span></label>
                <textarea name="reimburse_description" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow" rows="4" required placeholder="Describe the expense you are claiming for...">{{ old('description') }}</textarea>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Proof / Receipt <span class="text-red-500">*</span> <span class="text-gray-400 font-normal normal-case">(Max 2MB)</span></label>
                <input type="file" name="reimburse_proof" accept=".jpg,.jpeg,.png,.pdf" class="w-full bg-gray-50 border border-gray-200 text-gray-600 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium transition-shadow file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer" required>
                <div class="mt-2 text-xs text-gray-400 font-medium">Allowed formats: JPG, PNG, PDF.</div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100">
                <a href="{{ route('reimbursement.index') }}" class="inline-flex justify-center items-center bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 font-bold py-3 px-6 rounded-xl transition-colors no-underline">Cancel</a>
                <button type="submit" class="inline-flex justify-center items-center bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition-colors border-0 cursor-pointer text-base">
                    <i class="bi bi-send-fill mr-2"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
