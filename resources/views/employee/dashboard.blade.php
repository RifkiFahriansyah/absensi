@extends('layouts.employee')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Greeting Card --}}
    <div class="bg-gradient-to-r from-primary-600 via-primary-500 to-primary-400 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-primary-500/20 relative overflow-hidden">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative">
            <p class="text-primary-100 text-sm font-medium">Selamat datang,</p>
            <h2 class="text-2xl sm:text-3xl font-bold mt-1">{{ auth()->user()->name }} 👋</h2>
            <div class="flex flex-wrap items-center gap-4 mt-4">
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-sm font-medium" id="today-date">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium tabular-nums" id="live-clock">--:--:--</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Status Kehadiran Hari Ini</h3>

        @if($attendance)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Check In --}}
                <div class="bg-primary-50 rounded-2xl p-4 text-center">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Check In</p>
                    <p class="text-lg font-bold text-primary-700">{{ $attendance->check_in ?? '-' }}</p>
                </div>

                {{-- Check Out --}}
                <div class="bg-blue-50 rounded-2xl p-4 text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Check Out</p>
                    <p class="text-lg font-bold text-blue-700">{{ $attendance->check_out ?? '-' }}</p>
                </div>

                {{-- Status --}}
                <div class="{{ $attendance->status === 'hadir' ? 'bg-emerald-50' : 'bg-amber-50' }} rounded-2xl p-4 text-center">
                    <div class="w-10 h-10 {{ $attendance->status === 'hadir' ? 'bg-emerald-100' : 'bg-amber-100' }} rounded-xl flex items-center justify-center mx-auto mb-2">
                        @if($attendance->status === 'hadir')
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Status</p>
                    <p class="text-lg font-bold {{ $attendance->status === 'hadir' ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ ucfirst($attendance->status) }}
                    </p>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-gray-500 text-sm">Anda belum melakukan absensi hari ini</p>
            </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if(!$attendance || !$attendance->check_in)
            <a href="{{ route('employee.check-in') }}"
               class="group flex items-center gap-4 bg-white border-2 border-primary-200 hover:border-primary-400 rounded-2xl p-5 transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/10 hover:-translate-y-0.5">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Check In</p>
                    <p class="text-xs text-gray-500">Mulai absensi masuk</p>
                </div>
            </a>
        @endif

        @if($attendance && $attendance->check_in && !$attendance->check_out)
            <a href="{{ route('employee.check-out') }}"
               class="group flex items-center gap-4 bg-white border-2 border-blue-200 hover:border-blue-400 rounded-2xl p-5 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-0.5">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Check Out</p>
                    <p class="text-xs text-gray-500">Absensi pulang</p>
                </div>
            </a>
        @endif

        @if($attendance && $attendance->check_in && $attendance->check_out)
            <div class="sm:col-span-2 bg-primary-50 border border-primary-200 rounded-2xl px-6 py-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-primary-800">Absensi Lengkap</p>
                    <p class="text-sm text-primary-600">Anda sudah menyelesaikan absensi hari ini. Terima kasih!</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('live-clock').textContent = timeStr;
}
updateClock();
setInterval(updateClock, 1000);
</script>
@endpush
@endsection
