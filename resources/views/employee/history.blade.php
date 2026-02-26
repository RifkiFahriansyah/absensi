@extends('layouts.employee')

@section('page-title', 'Riwayat Kehadiran')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Kehadiran</h2>
            <p class="text-gray-500 text-sm">Lihat riwayat absensi bulanan Anda</p>
        </div>

        <form method="GET" action="{{ route('employee.history') }}" class="flex items-center gap-2">
            <input type="month" name="month" value="{{ $month }}"
                   class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition-colors">
                Filter
            </button>
        </form>
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($attendances->count())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($attendances as $attendance)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $attendance->check_in ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $attendance->check_out ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $attendance->status === 'hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($attendance->check_in_photo)
                                            <button type="button" onclick="showPhoto('{{ $attendance->check_in_photo }}', 'Check In')"
                                                    class="px-2.5 py-1.5 bg-primary-50 text-primary-700 text-xs font-medium rounded-lg hover:bg-primary-100 transition-colors">
                                                In
                                            </button>
                                        @endif
                                        @if($attendance->check_out_photo)
                                            <button type="button" onclick="showPhoto('{{ $attendance->check_out_photo }}', 'Check Out')"
                                                    class="px-2.5 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                                Out
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-gray-500 text-sm">Tidak ada data kehadiran untuk bulan ini</p>
            </div>
        @endif
    </div>
</div>

{{-- Photo Modal --}}
<div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 id="modal-title" class="text-lg font-bold text-gray-800"></h3>
            <button onclick="closePhoto()" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4">
            <img id="modal-photo" src="" alt="Selfie" class="w-full rounded-2xl">
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPhoto(src, title) {
    document.getElementById('modal-photo').src = src;
    document.getElementById('modal-title').textContent = 'Foto ' + title;
    const modal = document.getElementById('photo-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePhoto() {
    const modal = document.getElementById('photo-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('photo-modal').addEventListener('click', function(e) {
    if (e.target === this) closePhoto();
});
</script>
@endpush
@endsection
