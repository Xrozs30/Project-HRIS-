<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use Illuminate\Http\Request;

class HrReimbursementController extends Controller
{
    public function index()
    {
        $role = auth()->user()->employee_role;
        $roleToFetch = $role === 'owner' ? 'hr' : 'employee';

        // 1. Pending Reimbursements
        $pendingQuery = Reimbursement::select(
            'employee_id',
            \Illuminate\Support\Facades\DB::raw('MONTH(reimburse_date) as month'),
            \Illuminate\Support\Facades\DB::raw('YEAR(reimburse_date) as year'),
            \Illuminate\Support\Facades\DB::raw('SUM(reimburse_total) as total_amount'),
            \Illuminate\Support\Facades\DB::raw('COUNT(reimburse_id) as total_requests')
        )
        ->groupBy('employee_id', \Illuminate\Support\Facades\DB::raw('YEAR(reimburse_date)'), \Illuminate\Support\Facades\DB::raw('MONTH(reimburse_date)'))
        ->with('employee')
        ->whereHas('employee', function ($q) use ($roleToFetch) {
            $q->where('employee_role', $roleToFetch);
        });

        if ($role === 'hr') {
            $pendingQuery->where('reimburse_status', 'pending');
        } elseif ($role === 'owner') {
            $pendingQuery->where('reimburse_status', 'hr_approved');
        }

        $pendingReimbursements = $pendingQuery->get();

        // 2. History Reimbursements
        $historyQuery = Reimbursement::select(
            'employee_id',
            'reimburse_status',
            \Illuminate\Support\Facades\DB::raw('MONTH(reimburse_date) as month'),
            \Illuminate\Support\Facades\DB::raw('YEAR(reimburse_date) as year'),
            \Illuminate\Support\Facades\DB::raw('SUM(reimburse_total) as total_amount'),
            \Illuminate\Support\Facades\DB::raw('COUNT(reimburse_id) as total_requests')
        )
        ->groupBy('employee_id', 'reimburse_status', \Illuminate\Support\Facades\DB::raw('YEAR(reimburse_date)'), \Illuminate\Support\Facades\DB::raw('MONTH(reimburse_date)'))
        ->with('employee')
        ->whereHas('employee', function ($q) use ($roleToFetch) {
            $q->where('employee_role', $roleToFetch);
        });

        if ($role === 'hr') {
            $historyQuery->whereIn('reimburse_status', ['hr_approved', 'approved', 'rejected']);
        } elseif ($role === 'owner') {
            $historyQuery->whereIn('reimburse_status', ['approved', 'rejected']);
        }

        $historyReimbursements = $historyQuery->get();

        return view('hr.reimbursement.index', compact('pendingReimbursements', 'historyReimbursements'));
    }

    public function showMonth(Request $request)
    {
        $userId = $request->input('employee_id');
        $month = $request->input('month');
        $year = $request->input('year');
        $status = $request->input('status');

        $role = auth()->user()->employee_role;
        if (!$status) {
            $status = $role === 'owner' ? 'hr_approved' : 'pending';
        }

        $reimbursements = Reimbursement::where('employee_id', $userId)
            ->whereMonth('reimburse_date', $month)
            ->whereYear('reimburse_date', $year)
            ->where('reimburse_status', $status)
            ->get();

        return response()->json($reimbursements);
    }

    public function approveBatch(Request $request)
    {
        $userId = $request->input('employee_id');
        $month = $request->input('month');
        $year = $request->input('year');

        $role = auth()->user()->employee_role;
        $currentStatus = $role === 'owner' ? 'hr_approved' : 'pending';
        $newStatus = $role === 'owner' ? 'approved' : 'hr_approved';

        Reimbursement::where('employee_id', $userId)
            ->whereMonth('reimburse_date', $month)
            ->whereYear('reimburse_date', $year)
            ->where('reimburse_status', $currentStatus)
            ->update([
                'reimburse_status' => $newStatus,
            ]);

        if ($request->filled('notes')) {
            Reimbursement::where('employee_id', $userId)
                ->whereMonth('reimburse_date', $month)
                ->whereYear('reimburse_date', $year)
                ->update(['reimburse_notes' => \Illuminate\Support\Facades\DB::raw("CONCAT(COALESCE(reimburse_notes, ''), '\n', '" . addslashes($request->input('notes')) . "')")]);
        }

        return redirect()->back()->with('success', 'Reimbursements approved for the month.');
    }

    public function rejectBatch(Request $request)
    {
        $userId = $request->input('employee_id');
        $month = $request->input('month');
        $year = $request->input('year');

        $role = auth()->user()->employee_role;
        $currentStatus = $role === 'owner' ? 'hr_approved' : 'pending';
        
        Reimbursement::where('employee_id', $userId)
            ->whereMonth('reimburse_date', $month)
            ->whereYear('reimburse_date', $year)
            ->where('reimburse_status', $currentStatus)
            ->update([
                'reimburse_status' => 'rejected',
                'reimburse_notes' => $request->input('notes')
            ]);

        return redirect()->back()->with('success', 'Reimbursements rejected for the month.');
    }
}
