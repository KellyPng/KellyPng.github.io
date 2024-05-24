<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employeeTypes = DB::table('users')->distinct()->pluck('employeeType')->all();
        $search = $request->input('search');
        $query = User::query();
        if ($search) {
            // Adjust the conditions based on your search requirements
            $query->where('firstname', 'like', "%$search%")
                ->orWhere('lastname', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }

        // Fetch all matching records
        $employees = $query->get();
        //$employees = User::all();
        return view('employees.index',compact('employees','employeeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(),[
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'emptype' => ['required'],
            'password' => ['required','string','min:8',
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $employee = new Employee;
        $employee->firstname = $request->input('fname');
        $employee->lastname = $request->input('lname');
        $employee->email = $request->input('email');
        $employee->employeeType = $request->input('emptype');
        $employee->password = Hash::make($request->input('password'));
        $employee->save();
        return redirect('employees')->with('success', 'Employee Created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::query()->find((int)$id);
        return view('employees.edit',compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::query()->find((int)$id);
        $validator = FacadesValidator::make($request->all(),[
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => 'required|email|unique:users,email,' . $employee->id . ',id',
            'emptype' => ['required'],
            'password' => ['nullable','string','min:8',
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $employee->firstname = $request->input('fname');
        $employee->lastname = $request->input('lname');
        $employee->email = $request->input('email');
        $employee->employeeType = $request->input('emptype');
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->input('password'));
        }
        $employee->save();
        return redirect('employees')->with('success', 'Employee Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::query()->find((int)$id);
        $employee->delete();
        return redirect('employees')->with('success', 'Employee Deleted!');
    }
}
