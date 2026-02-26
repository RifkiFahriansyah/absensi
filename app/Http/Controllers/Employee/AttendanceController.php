<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\GpsService;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkInForm()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            return redirect()->route('employee.dashboard')
                ->with('warning', 'Anda sudah melakukan check-in hari ini.');
        }

        $settings = Setting::instance();

        return view('employee.check-in', compact('settings'));
    }

    public function checkIn(CheckInRequest $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->first();

        if ($existing) {
            return redirect()->route('employee.dashboard')
                ->with('warning', 'Anda sudah melakukan check-in hari ini.');
        }

        $settings = Setting::instance();
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        if (!GpsService::isWithinRadius(
            $latitude,
            $longitude,
            (float) $settings->office_lat,
            (float) $settings->office_long,
            $settings->radius_meter
        )) {
            $distance = GpsService::getDistanceFromOffice(
                $latitude,
                $longitude,
                (float) $settings->office_lat,
                (float) $settings->office_long
            );

            return back()->with('error', "Anda berada di luar radius kantor ($distance dari kantor). Maksimal {$settings->radius_meter} meter.");
        }

        $now = Carbon::now();
        $workStart = Carbon::createFromTimeString($settings->work_start);
        $lateThreshold = $workStart->copy()->addMinutes($settings->late_tolerance_minutes);

        $status = $now->gt($lateThreshold) ? 'telat' : 'hadir';

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now->toTimeString(),
            'check_in_lat' => $latitude,
            'check_in_long' => $longitude,
            'check_in_photo' => $request->photo,
            'status' => $status,
        ]);

        $message = $status === 'telat'
            ? 'Check-in berhasil, tetapi Anda terlambat.'
            : 'Check-in berhasil! Selamat bekerja.';

        return redirect()->route('employee.dashboard')->with('success', $message);
    }

    public function checkOutForm()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('employee.dashboard')
                ->with('warning', 'Anda belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out) {
            return redirect()->route('employee.dashboard')
                ->with('warning', 'Anda sudah melakukan check-out hari ini.');
        }

        $settings = Setting::instance();

        return view('employee.check-out', compact('settings', 'attendance'));
    }

    public function checkOut(CheckOutRequest $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return redirect()->route('employee.dashboard')
                ->with('warning', 'Tidak dapat melakukan check-out.');
        }

        $settings = Setting::instance();
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        if (!GpsService::isWithinRadius(
            $latitude,
            $longitude,
            (float) $settings->office_lat,
            (float) $settings->office_long,
            $settings->radius_meter
        )) {
            $distance = GpsService::getDistanceFromOffice(
                $latitude,
                $longitude,
                (float) $settings->office_lat,
                (float) $settings->office_long
            );

            return back()->with('error', "Anda berada di luar radius kantor ($distance dari kantor). Maksimal {$settings->radius_meter} meter.");
        }

        $attendance->update([
            'check_out' => Carbon::now()->toTimeString(),
            'check_out_lat' => $latitude,
            'check_out_long' => $longitude,
            'check_out_photo' => $request->photo,
        ]);

        return redirect()->route('employee.dashboard')->with('success', 'Check-out berhasil! Selamat istirahat.');
    }

    public function history()
    {
        $user = auth()->user();
        $month = request('month', Carbon::now()->format('Y-m'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc')
            ->get();

        return view('employee.history', compact('attendances', 'month'));
    }
}