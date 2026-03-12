<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollTransaction;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {

        $employees = Employee::latest()->paginate(15);
        return view('employees.index', compact('employees' ));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request )
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric',
        ]);

        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Employee created.');
    }

    public function show(Employee $employee   )
    {

        return view('employees.show', compact('employee' ));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric',
        ]);

        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
