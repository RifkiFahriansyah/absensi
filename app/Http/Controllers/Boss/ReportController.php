<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class ReportController extends Controller
{
    public function index()
    {
        $month = request('month', Carbon::now()->format('Y-m'));
        $employeeId = request('employee_id');

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        $query = Attendance::with('user')
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc');

        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        $attendances = $query->get();

        // Summary stats
        $totalPresent = $attendances->where('status', 'hadir')->count();
        $totalLate = $attendances->where('status', 'telat')->count();

        return view('boss.reports.index', compact(
            'attendances',
            'employees',
            'month',
            'employeeId',
            'totalPresent',
            'totalLate'
        ));
    }

    public function export()
    {
        $month = request('month', Carbon::now()->format('Y-m'));
        $employeeId = request('employee_id');

        $filename = 'laporan_absensi_' . $month;
        if ($employeeId) {
            $employee = User::find($employeeId);
            $filename .= '_' . str_replace(' ', '_', $employee->name ?? 'unknown');
        }
        $filename .= '.xlsx';

        return Excel::download(new AttendanceExport($month, $employeeId), $filename);
    }
}
