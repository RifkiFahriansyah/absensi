<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class MonitorController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $employees = User::where('role', 'employee')
            ->with(['attendances' => function ($query) use ($today) {
                $query->where('date', $today);
            }])
            ->get()
            ->map(function ($employee) {
                $attendance = $employee->attendances->first();
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'check_in' => $attendance?->check_in,
                    'check_out' => $attendance?->check_out,
                    'status' => $attendance?->status ?? 'absent',
                    'check_in_photo' => $attendance?->check_in_photo,
                ];
            });

        return view('boss.monitor', compact('employees'));
    }
}