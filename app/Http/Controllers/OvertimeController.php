<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Overtime;
use App\Models\Employee;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = auth()->user()->overtimes()->orderBy('overtime_date', 'desc')->paginate(10);
        return view('overtime.index', compact('overtimes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'overtime_date' => 'required|date',
            'overtime_start' => 'required',
            'overtime_finish' => 'required',
            'overtime_description' => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse($request->overtime_start);
        $finish = \Carbon\Carbon::parse($request->overtime_finish);
        $durationHours = $start->diffInMinutes($finish) / 60;

        $validated['overtime_duration'] = $durationHours;
        $validated['overtime_status'] = 'pending';

        auth()->user()->overtimes()->create($validated);

        return redirect()->route('overtime.index')->with('success', 'Overtime request submitted successfully.');
    }
}
