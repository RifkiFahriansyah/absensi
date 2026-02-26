<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->paginate(15);

        return view('boss.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('boss.employees.create');
    }

    public function store(EmployeeRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'employee',
        ]);

        return redirect()->route('boss.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $employee)
    {
        return view('boss.employees.edit', compact('employee'));
    }

    public function update(EmployeeRequest $request, User $employee)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $employee->update($data);

        return redirect()->route('boss.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->route('boss.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $employee)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee->update([
            'password' => $request->password,
        ]);

        return redirect()->route('boss.employees.index')
            ->with('success', 'Password berhasil direset.');
    }
}
