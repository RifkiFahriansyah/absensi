<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\Leave;

class LeaveApprovalController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('boss.leaves.index', compact('leaves'));
    }

    public function approve(Leave $leave)
    {
        $leave->update(['status' => 'approved']);

        return redirect()->route('boss.leaves.index')
            ->with('success', 'Cuti berhasil disetujui.');
    }

    public function reject(Leave $leave)
    {
        $leave->update(['status' => 'rejected']);

        return redirect()->route('boss.leaves.index')
            ->with('success', 'Cuti berhasil ditolak.');
    }
}
