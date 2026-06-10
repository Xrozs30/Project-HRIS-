<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Overtime;

class HrOvertimeController extends Controller
{
    public function index()
    {
        $roleToFetch = auth()->user()->employee_role === 'owner' ? 'hr' : 'employee';

        $baseQuery = function ($status, $pageName) use ($roleToFetch) {
            $q = \App\Models\Overtime::whereHas('employee', function ($query) use ($roleToFetch) {
                    $query->where('employee_role', $roleToFetch);
                })
                ->with(['employee'])
                ->orderBy('overtime_create_at', 'desc');
            if ($status === 'pending') $q->where('overtime_status', 'pending');
            else $q->whereIn('overtime_status', ['approved', 'rejected']);
            return $q->paginate(10, ['*'], $pageName)->withQueryString();
        };

        $pendingOvertimes = $baseQuery('pending', 'pending_page');
        $historyOvertimes = $baseQuery('history', 'history_page');

        // Attach attendance data
        foreach ($pendingOvertimes as $ot) {
            $ot->attendance = \App\Models\Presence::where('employee_id', $ot->employee_id)->where('presence_date', $ot->overtime_date)->first();
        }
        foreach ($historyOvertimes as $ot) {
            $ot->attendance = \App\Models\Presence::where('employee_id', $ot->employee_id)->where('presence_date', $ot->overtime_date)->first();
        }

        return view('hr.overtime.index', compact('pendingOvertimes', 'historyOvertimes'));
    }

    public function approve($id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'overtime_status' => 'approved',
            'overtime_approve_by' => auth()->user()->employee_id
        ]);
        return back()->with('success', 'Overtime approved successfully.');
    }

    public function reject($id)
    {
        $overtime = Overtime::findOrFail($id);
        $overtime->update([
            'overtime_status' => 'rejected',
            'overtime_approve_by' => auth()->user()->employee_id
        ]);
        return back()->with('success', 'Overtime rejected successfully.');
    }
}
