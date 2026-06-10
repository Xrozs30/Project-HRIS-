<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $roles = auth()->user()->employee_role === 'owner' ? ['employee', 'hr'] : ['employee'];
        $employees = \App\Models\Employee::whereIn('employee_role', $roles)->orderBy('employee_name')->paginate(10);
        return view('karyawan.index', compact('employees'));
    }

    public function create()
    {
        // Generate next employee_id in ST0001 format
        $lastEmployee = \App\Models\Employee::orderBy('employee_id', 'desc')
            ->where('employee_id', 'like', 'ST%')
            ->first();

        if ($lastEmployee) {
            $number = (int) substr($lastEmployee->employee_id, 2);
            $newNik = 'ST' . str_pad($number + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNik = 'ST01';
        }

        $positions = \App\Models\Position::all();
        $taxes = \App\Models\Tax::all();

        return view('karyawan.create', compact('newNik', 'positions', 'taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_nik'          => 'required|string|unique:employees,employee_nik',
            'employee_name'         => 'required|string|max:255',
            'employee_email'        => 'required|email|unique:employees,employee_email',
            'employee_phone'        => 'nullable|string',
            'employee_address'       => 'nullable|string',
            'position_id'           => 'nullable|exists:positions,position_id',
            'employee_password'     => 'required|string|min:6',
            'tax_id'                => 'required|exists:taxes,tax_id',
            'employee_basic_salary' => 'required|numeric',
            'employee_bank_number'  => 'nullable|string',
            'employee_bank_name'    => 'nullable|string',
            'employee_bpjs_number'  => 'nullable|string',
            'employee_gender'       => 'nullable|in:male,female',
            'employee_birth_date'   => 'nullable|date',
        ]);

        $role = 'employee';
        if (auth()->user()->employee_role === 'owner' && $request->employee_role === 'hr') {
            $role = 'hr';
        }

        // Auto-generate employee_id in ST0001 format
        $lastEmployee = \App\Models\Employee::orderBy('employee_id', 'desc')
            ->where('employee_id', 'like', 'ST%')
            ->first();
        if ($lastEmployee) {
            $number = (int) substr($lastEmployee->employee_id, 2);
            $employeeId = 'ST' . str_pad($number + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $employeeId = 'ST01';
        }

        \App\Models\Employee::create([
            'employee_id'           => $employeeId,
            'employee_nik'          => $request->employee_nik,
            'employee_name'         => $request->employee_name,
            'employee_email'        => $request->employee_email,
            'employee_phone'        => $request->employee_phone,
            'employee_address'       => $request->employee_address,
            'position_id'           => $request->position_id,
            'employee_password'     => bcrypt($request->employee_password),
            'role'                  => $role,
            'tax_id'                => $request->tax_id,
            'employee_basic_salary' => $request->employee_basic_salary,
            'employee_bank_number'  => $request->employee_bank_number,
            'employee_bank_name'    => $request->employee_bank_name,
            'employee_bpjs_number'  => $request->employee_bpjs_number,
            'employee_gender'       => $request->employee_gender,
            'employee_birth_date'   => $request->employee_birth_date,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Employee successfully added.');
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        if (auth()->user()->employee_role === 'owner' && $employee->employee_role === 'employee') {
            abort(403, 'Owners cannot edit details of ordinary employees.');
        }
        $positions = \App\Models\Position::all();
        $taxes = \App\Models\Tax::all();
        return view('karyawan.edit', compact('employee', 'positions', 'taxes'));
    }

    public function update(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $request->validate([
            'employee_nik'          => 'required|string|unique:employees,employee_nik,' . $id . ',employee_id',
            'employee_name'         => 'required|string|max:255',
            'employee_email'        => 'required|email|unique:employees,employee_email,' . $id . ',employee_id',
            'employee_phone'        => 'nullable|string',
            'employee_address'       => 'nullable|string',
            'position_id'           => 'nullable|exists:positions,position_id',
            'employee_password'     => 'nullable|string|min:6',
            'tax_id'                => 'required|exists:taxes,tax_id',
            'employee_basic_salary' => 'required|numeric',
            'employee_face_descriptor' => 'nullable|string',
            'employee_bank_number'  => 'nullable|string',
            'employee_bank_name'    => 'nullable|string',
            'employee_bpjs_number'  => 'nullable|string',
            'employee_gender'       => 'nullable|in:male,female',
            'employee_birth_date'   => 'nullable|date',
        ]);

        $data = [
            'employee_nik'          => $request->employee_nik,
            'employee_name'         => $request->employee_name,
            'employee_email'        => $request->employee_email,
            'employee_phone'        => $request->employee_phone,
            'employee_address'       => $request->employee_address,
            'position_id'           => $request->position_id,
            'tax_id'                => $request->tax_id,
            'employee_basic_salary' => $request->employee_basic_salary,
            'employee_bank_number'  => $request->employee_bank_number,
            'employee_bank_name'    => $request->employee_bank_name,
            'employee_bpjs_number'  => $request->employee_bpjs_number,
            'employee_gender'       => $request->employee_gender,
            'employee_birth_date'   => $request->employee_birth_date,
        ];

        if ($request->filled('employee_password')) {
            $data['employee_password'] = bcrypt($request->employee_password);
        }

        if ($request->filled('employee_face_descriptor')) {
            $data['employee_face_descriptor'] = $request->employee_face_descriptor;
        }

        $employee->update($data);

        return redirect()->route('karyawan.index')->with('success', 'Employee data successfully updated.');
    }

    public function destroy($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('karyawan.index')->with('success', 'Employee successfully deleted.');
    }
}