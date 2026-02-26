@extends('layouts.boss')

@section('page-title', 'Persetujuan Cuti')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Persetujuan Cuti</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola pengajuan cuti karyawan</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($leaves->count())
            <div class="divide-y divide-gray-50">
                @foreach($leaves as $leave)
                    <div class="p-5 hover:bg-gray-50/50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($leave->user->name ?? '-', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $leave->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} —
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 ml-11">{{ $leave->reason }}</p>
                            </div>

                            <div class="flex items-center gap-2 ml-11 sm:ml-0">
                                @if($leave->status === 'pending')
                                    <form method="POST" action="{{ route('boss.leaves.approve', $leave) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('boss.leaves.reject', $leave) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-xl hover:bg-red-700 transition-colors">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $leave->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $leaves->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-gray-500 text-sm">Tidak ada pengajuan cuti</p>
            </div>
        @endif
    </div>
</div>
@endsection
