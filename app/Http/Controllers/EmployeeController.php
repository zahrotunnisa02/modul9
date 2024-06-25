<?php

namespace App\Http\Controllers;


use App\Models\Position;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Employee List';
        // Query Builder
       // ELOQUENT
    $employees = Employee::all();

    return view('employee.index', [
        'pageTitle' => $pageTitle,
        'employees' => $employees
    ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Employee';

        // ELOQUENT
        $positions = Position::all();

        return view('employee.create', compact('pageTitle', 'positions'));
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get File
        $file = $request->file('cv');

        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store File
            $file->store('public/files');
        }

        // ELOQUENT
        $employee = New Employee;
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        return redirect()->route('employees.index');

    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        $pageTitle = 'Employee Detail';

        // ELOQUENT
        $employee = Employee::find($id);

        return view('employee.show', compact('pageTitle', 'employee'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
{
    $pageTitle = 'Edit Employee';

    // ELOQUENT
    $positions = Position::all();
    $employee = Employee::find($id);

    return view('employee.edit', compact('pageTitle', 'positions', 'employee'));
}

public function update(Request $request, string $id)
{
    $messages = [
        'required' => ':Attribute harus diisi.',
        'email' => 'Isi :attribute dengan format yang benar',
        'numeric' => 'Isi :attribute dengan angka'
    ];

    $validator = Validator::make($request->all(), [
        'firstName' => 'required',
        'lastName' => 'required',
        'email' => 'required|email',
        'age' => 'required|numeric',
    ], $messages);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // ELOQUENT
    $employee = Employee::find($id);
    $employee->firstname = $request->firstName;
    $employee->lastname = $request->lastName;
    $employee->email = $request->email;
    $employee->age = $request->age;
    $employee->position_id = $request->position;

    if ($request->hasFile('cv')) {
        $file = $request->file('cv');
        $originalFilename = $file->getClientOriginalName();
        $encryptedFilename = $file->hashName();
        $file->store('public/files');
        $employee->original_filename = $originalFilename;
        $employee->encrypted_filename = $encryptedFilename;

        // Hapus file CV lama jika ada
        if (!empty($employee->encrypted_filename)) {
            Storage::delete('public/files/' . $employee->encrypted_filename);
        }
    }

    $employee->save();

    return redirect()->route('employees.index');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    // ELOQUENT
    // Temukan employee berdasarkan ID
    $employee = Employee::find($id);

    if ($employee) {
        // Hapus file CV jika ada
        if (!empty($employee->encrypted_filename)) {
            Storage::delete('public/files/' . $employee->encrypted_filename);
        }

        // Hapus employee dari database
        $employee->delete();
    }

    // Redirect ke halaman daftar employee
    return redirect()->route('employees.index');
}


public function downloadFile($employeeId)
{
    $employee = Employee::find($employeeId);
    $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
    $downloadFilename = Str::lower($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

    if(Storage::exists($encryptedFilename)) {
        return Storage::download($encryptedFilename, $downloadFilename);
    }
}

}
