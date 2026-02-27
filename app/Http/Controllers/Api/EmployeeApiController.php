<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Setting;
use App\Services\GpsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'attendance' => $attendance,
        ]);
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah melakukan check-in hari ini.'
            ], 422);
        }

        $onLeave = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($onLeave) {
            return response()->json([
                'message' => 'Anda tidak bisa absen karena sedang dalam masa cuti yang disetujui.'
            ], 422);
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

            return response()->json([
                'message' => "Anda berada di luar radius kantor ($distance dari kantor). Maksimal {$settings->radius_meter} meter."
            ], 422);
        }

        $now = Carbon::now();
        $workStart = Carbon::createFromTimeString($settings->work_start);
        $lateThreshold = $workStart->copy()->addMinutes($settings->late_tolerance_minutes);

        $status = $now->gt($lateThreshold) ? 'telat' : 'hadir';

        $attendance = Attendance::create([
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

        return response()->json([
            'message' => $message,
            'attendance' => $attendance,
        ]);
    }

    public function checkOutInfo(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'message' => 'Anda belum melakukan check-in hari ini.'
            ], 422);
        }

        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Anda sudah melakukan check-out hari ini.'
            ], 422);
        }

        return response()->json([
            'attendance' => $attendance,
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string',
        ]);

        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'Tidak dapat melakukan check-out.'
            ], 422);
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

            return response()->json([
                'message' => "Anda berada di luar radius kantor ($distance dari kantor). Maksimal {$settings->radius_meter} meter."
            ], 422);
        }

        $attendance->update([
            'check_out' => Carbon::now()->toTimeString(),
            'check_out_lat' => $latitude,
            'check_out_long' => $longitude,
            'check_out_photo' => $request->photo,
        ]);

        return response()->json([
            'message' => 'Check-out berhasil! Selamat istirahat.',
            'attendance' => $attendance,
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', Carbon::parse($month)->year)
            ->whereMonth('date', Carbon::parse($month)->month)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($attendance) {
                // Determine photo URLs, accounting for base64 data strings that might be saved
                $attendance->check_in_photo = $attendance->check_in_photo ? (str_starts_with($attendance->check_in_photo, 'data:image') ? $attendance->check_in_photo : url('storage/' . $attendance->check_in_photo)) : null;
                $attendance->check_out_photo = $attendance->check_out_photo ? (str_starts_with($attendance->check_out_photo, 'data:image') ? $attendance->check_out_photo : url('storage/' . $attendance->check_out_photo)) : null;
                return $attendance;
            });

        return response()->json([
            'attendances' => $attendances,
            'month' => $month,
        ]);
    }

    public function leaves(Request $request)
    {
        $leaves = Leave::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($leaves);
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $leave = Leave::create([
            'user_id' => $request->user()->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan cuti berhasil dikirim.',
            'leave' => $leave,
        ]);
    }
}
