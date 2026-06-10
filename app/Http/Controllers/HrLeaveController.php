<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HrLeaveController extends Controller
{
    public function index()
    {
        $roleToFetch = auth()->user()->employee_role === 'owner' ? 'hr' : 'employee';

        $pendingRequests = \App\Models\LeavePermission::whereHas('employee', function ($query) use ($roleToFetch) {
                $query->where('employee_role', $roleToFetch);
            })
            ->where('leave_status', 'pending')
            ->with('employee')
            ->orderBy('leave_create_at', 'desc')
            ->paginate(10, ['*'], 'pending_page')->withQueryString();

        $historyRequests = \App\Models\LeavePermission::whereHas('employee', function ($query) use ($roleToFetch) {
                $query->where('employee_role', $roleToFetch);
            })
            ->whereIn('leave_status', ['approved', 'rejected'])
            ->with('employee')
            ->orderBy('leave_create_at', 'desc')
            ->paginate(10, ['*'], 'history_page')->withQueryString();

        return view('hr.leave.index', compact('pendingRequests', 'historyRequests'));
    }

    public function approve($id)
    {
        $request = \App\Models\LeavePermission::findOrFail($id);
        $request->update([
            'leave_status' => 'approved',
            'leave_approve_by' => auth()->user()->employee_id,
            'leave_approve_at' => now()
        ]);

        return redirect()->back()->with('success', 'Application successfully approved.');
    }

    public function reject(Request $request, $id)
    {
        $leavePermission = \App\Models\LeavePermission::findOrFail($id);
        $leavePermission->update([
            'leave_status' => 'rejected',
            'leave_approve_by' => auth()->user()->employee_id,
            'leave_rejection_reason' => $request->leave_rejection_reason,
            'leave_approve_at' => now()
        ]);

        return redirect()->back()->with('success', 'Application successfully rejected.');
    }
}
