<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BossApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $totalEmployees = User::where('role', 'employee')->count();

        $presentToday = Attendance::where('date', $today)
            ->whereNotNull('check_in')
            ->count();

        $lateToday = Attendance::where('date', $today)
            ->where('status', 'telat')
            ->count();

        $absentToday = $totalEmployees - $presentToday;

        // Monthly chart data
        $month = $request->query('chart_month', Carbon::now()->format('Y-m'));
        $year = Carbon::parse($month)->year;
        $monthNum = Carbon::parse($month)->month;
        $daysInMonth = Carbon::parse($month)->daysInMonth;

        $chartData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $monthNum, $day)->toDateString();
            $present = Attendance::where('date', $date)->where('status', 'hadir')->count();
            $late = Attendance::where('date', $date)->where('status', 'telat')->count();

            $chartData[] = [
                'date' => $day,
                'present' => $present,
                'late' => $late,
            ];
        }

        return response()->json([
            'totalEmployees' => $totalEmployees,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'absentToday' => $absentToday,
            'chartData' => $chartData,
            'month' => $month,
        ]);
    }

    public function monitor()
    {
        $today = Carbon::today()->toDateString();

        $employees = User::where('role', 'employee')
            ->with(['attendances' => function ($query) use ($today) {
                $query->where('date', $today);
            }, 'leaves' => function ($query) use ($today) {
                $query->where('status', 'approved')
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
            }])
            ->get()
            ->map(function ($employee) {
                $attendance = $employee->attendances->first();
                $onLeave = $employee->leaves->isNotEmpty();

                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'check_in' => $attendance?->check_in,
                    'check_out' => $attendance?->check_out,
                    'check_in_photo' => $attendance?->check_in_photo ? (str_starts_with($attendance->check_in_photo, 'data:image') ? $attendance->check_in_photo : url('storage/' . $attendance->check_in_photo)) : null,
                    'check_out_photo' => $attendance?->check_out_photo ? (str_starts_with($attendance->check_out_photo, 'data:image') ? $attendance->check_out_photo : url('storage/' . $attendance->check_out_photo)) : null,
                    'status' => $onLeave ? 'cuti' : ($attendance?->status ?? 'absent'),
                ];
            });

        return response()->json([
            'employees' => $employees,
            'date' => now()->translatedFormat('l, d F Y'),
        ]);
    }

    public function employees()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->paginate(15);

        return response()->json($employees);
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => 'employee',
        ]);

        return response()->json([
            'message' => 'Karyawan berhasil ditambahkan.',
            'employee' => $employee,
        ]);
    }

    public function showEmployee(User $employee)
    {
        return response()->json($employee);
    }

    public function updateEmployee(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $employee->update($data);

        return response()->json([
            'message' => 'Data karyawan berhasil diperbarui.',
            'employee' => $employee,
        ]);
    }

    public function destroyEmployee(User $employee)
    {
        $employee->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus.'
        ]);
    }

    public function resetPassword(Request $request, User $employee)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'message' => 'Password berhasil direset.'
        ]);
    }

    public function leaves()
    {
        $leaves = Leave::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($leaves);
    }

    public function approveLeave(Leave $leave)
    {
        $leave->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Cuti berhasil disetujui.'
        ]);
    }

    public function rejectLeave(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Cuti berhasil ditolak.'
        ]);
    }

    public function reports(Request $request)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $employeeId = $request->query('employee_id');

        $employees = User::where('role', 'employee')->orderBy('name')->get(['id', 'name']);

        $query = Attendance::with('user')
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $attendances = $query->get();

        $leaves = Leave::with('user')
            ->where('status', 'approved')
            ->where(function($q) use ($month) {
                // Leaves that overlap with this month
                $q->whereYear('start_date', Carbon::parse($month)->year)
                  ->whereMonth('start_date', Carbon::parse($month)->month)
                  ->orWhere(function($q2) use ($month) {
                      $q2->whereYear('end_date', Carbon::parse($month)->year)
                         ->whereMonth('end_date', Carbon::parse($month)->month);
                  });
            });

        if ($employeeId) {
            $leaves->where('user_id', $employeeId);
        }

        $approvedLeaves = $leaves->get();
        // Convert leaves into faux attendance rows for the report
        foreach ($approvedLeaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            
            // Limit the loop to the requested month bounds
            $monthStart = Carbon::parse($month)->startOfMonth();
            $monthEnd = Carbon::parse($month)->endOfMonth();
            
            if ($start->lt($monthStart)) $start = $monthStart;
            if ($end->gt($monthEnd)) $end = $monthEnd;

            for ($date = $start; $date->lte($end); $date->addDay()) {
                // If we don't already have an attendance record for this day (to avoid overriding check-ins if they somehow checked in first)
                if (!$attendances->where('user_id', $leave->user_id)->where('date', $date->toDateString())->count()) {
                    $newAttendance = new Attendance([
                        'id' => 'leave_' . $leave->id . '_' . $date->toDateString(),
                        'user_id' => $leave->user_id,
                        'date' => $date->toDateString(),
                        'status' => 'cuti',
                    ]);
                    $newAttendance->setRelation('user', $leave->user);
                    // Append extra mock data manually to the model to pass it through JSON
                    $newAttendance->leave_start = $leave->start_date;
                    $newAttendance->leave_end = $leave->end_date;
                    $attendances->push($newAttendance);
                }
            }
        }

        // Sort collection natively by date descending
        $attendances = $attendances->sortByDesc('date')->values();

        $totalPresent = $attendances->where('status', 'hadir')->count();
        $totalLate = $attendances->where('status', 'telat')->count();
        $totalCuti = $attendances->where('status', 'cuti')->count();

        return response()->json([
            'attendances' => $attendances,
            'employees' => $employees,
            'month' => $month,
            'employeeId' => $employeeId,
            'totalPresent' => $totalPresent,
            'totalLate' => $totalLate,
            'totalCuti' => $totalCuti,
        ]);
    }

    public function settings()
    {
        $settings = Setting::instance();
        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'office_lat' => 'required|numeric',
            'office_long' => 'required|numeric',
            'radius_meter' => 'required|integer|min:10|max:100000',
            'work_start' => 'required',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
        ]);

        $settings = Setting::instance();
        $settings->update($request->only([
            'office_lat', 'office_long', 'radius_meter',
            'work_start', 'late_tolerance_minutes'
        ]));

        return response()->json([
            'message' => 'Pengaturan berhasil disimpan.',
            'settings' => $settings,
        ]);
    }
}
