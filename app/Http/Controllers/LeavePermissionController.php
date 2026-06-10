<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeavePermissionController extends Controller
{
    public function index()
    {
        $requests = \App\Models\LeavePermission::where('employee_id', auth()->user()->employee_id)
            ->orderBy('leave_create_at', 'desc')
            ->paginate(10);

        return view('leave.index', compact('requests'));
    }

    public function create()
    {
        return view('leave.create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:cuti,ijin,sakit',
            'leave_start_date' => 'required|date|after_or_equal:today',
            'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
            'leave_reason' => 'required|string',
            'leave_sick_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $start = \Carbon\Carbon::parse($request->leave_start_date);
        $end = \Carbon\Carbon::parse($request->leave_end_date);
        $duration = $start->diffInDays($end) + 1;

        if ($request->leave_type !== 'sakit') {
            $currentMonthLeaves = \App\Models\LeavePermission::where('employee_id', auth()->user()->employee_id)
                ->where('leave_type', '!=', 'sakit')
                ->whereMonth('leave_start_date', $start->month)
                ->whereYear('leave_start_date', $start->year)
                ->where('leave_status', '!=', 'rejected')
                ->sum('leave_duration');

            if (($currentMonthLeaves + $duration) > 4) {
                $remaining = max(0, 4 - $currentMonthLeaves);
                return back()->withErrors(['leave_start_date' => "Monthly leave/permission total exceeded (Maximum 4 days per month). You only have $remaining day(s) left."])->withInput();
            }
        } else {
            // Sick leave quota: max 2 per month
            $currentMonthSickLeaves = \App\Models\LeavePermission::where('employee_id', auth()->user()->employee_id)
                ->where('leave_type', 'sakit')
                ->whereMonth('leave_start_date', $start->month)
                ->whereYear('leave_start_date', $start->year)
                ->where('leave_status', '!=', 'rejected')
                ->count();

            if ($currentMonthSickLeaves >= 2) {
                return back()->withErrors(['leave_start_date' => 'Sick leave quota exceeded. Maximum 2 sick leave requests per month.'])->withInput();
            }
        }

        $path = null;
        if ($request->hasFile('leave_sick_proof')) {
            $path = $request->file('leave_sick_proof')->store('proofs', 'public');
        }

        \App\Models\LeavePermission::create([
            'employee_id' => auth()->user()->employee_id,
            'leave_type' => $request->leave_type,
            'leave_start_date' => $request->leave_start_date,
            'leave_end_date' => $request->leave_end_date,
            'leave_duration' => $duration,
            'leave_reason' => $request->leave_reason,
            'leave_sick_proof' => $path,
            'leave_status' => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Application successfully submitted.');
    }
}
