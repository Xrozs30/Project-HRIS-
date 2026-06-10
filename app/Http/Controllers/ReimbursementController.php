<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReimbursementController extends Controller
{
    public function index()
    {
        $reimbursements = Reimbursement::where('employee_id', auth()->user()->employee_id)->orderBy('reimburse_date', 'desc')->paginate(10);
        return view('reimbursement.index', compact('reimbursements'));
    }

    public function create()
    {
        return view('reimbursement.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reimburse_date' => 'required|date',
            'reimburse_total' => 'required|numeric|min:0',
            'reimburse_description' => 'required|string',
            'reimburse_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('reimburse_proof')) {
            $filePath = $request->file('reimburse_proof')->store('reimbursement_files', 'public');
        }

        Reimbursement::create([
            'employee_id' => auth()->user()->employee_id,
            'reimburse_date' => $request->reimburse_date,
            'reimburse_total' => $request->reimburse_total,
            'reimburse_description' => $request->reimburse_description,
            'reimburse_proof' => $filePath,
            'reimburse_status' => 'pending',
        ]);

        return redirect()->route('reimbursement.index')->with('success', 'Reimbursement request submitted successfully.');
    }
}
