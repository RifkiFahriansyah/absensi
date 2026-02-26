<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
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
        $month = request('chart_month', Carbon::now()->format('Y-m'));
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

        return view('boss.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'lateToday',
            'absentToday',
            'chartData',
            'month'
        ));
    }
}
