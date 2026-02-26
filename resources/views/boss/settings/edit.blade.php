@extends('layouts.boss')

@section('page-title', 'Pengaturan')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan</h2>
        <p class="text-gray-500 text-sm mt-1">Konfigurasi lokasi kantor dan jadwal kerja</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('boss.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Office Location --}}
            <div>
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Kantor
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="office_lat" class="block text-xs font-semibold text-gray-500 mb-1.5">Latitude</label>
                        <input type="number" id="office_lat" name="office_lat" step="0.0000001"
                               value="{{ old('office_lat', $settings->office_lat) }}" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                        @error('office_lat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="office_long" class="block text-xs font-semibold text-gray-500 mb-1.5">Longitude</label>
                        <input type="number" id="office_long" name="office_long" step="0.0000001"
                               value="{{ old('office_long', $settings->office_long) }}" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                        @error('office_long') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="button" onclick="getCurrentLocation()"
                        class="mt-3 w-full py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Gunakan Lokasi Saat Ini
                </button>
            </div>

            {{-- Radius --}}
            <div>
                <label for="radius_meter" class="block text-sm font-semibold text-gray-700 mb-2">Radius (meter)</label>
                <input type="number" id="radius_meter" name="radius_meter" min="10" max="1000"
                       value="{{ old('radius_meter', $settings->radius_meter) }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                <p class="text-xs text-gray-400 mt-1">Jarak maksimal karyawan dari titik kantor untuk absensi</p>
                @error('radius_meter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Work Schedule --}}
            <div>
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jadwal Kerja
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="work_start" class="block text-xs font-semibold text-gray-500 mb-1.5">Jam Masuk</label>
                        <input type="time" id="work_start" name="work_start"
                               value="{{ old('work_start', is_string($settings->work_start) ? $settings->work_start : $settings->work_start) }}" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                        @error('work_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="late_tolerance_minutes" class="block text-xs font-semibold text-gray-500 mb-1.5">Toleransi Keterlambatan (menit)</label>
                        <input type="number" id="late_tolerance_minutes" name="late_tolerance_minutes" min="0" max="120"
                               value="{{ old('late_tolerance_minutes', $settings->late_tolerance_minutes) }}" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                        @error('late_tolerance_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-primary-500/25 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert('GPS tidak didukung di browser ini.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById('office_lat').value = position.coords.latitude.toFixed(7);
            document.getElementById('office_long').value = position.coords.longitude.toFixed(7);
        },
        function(error) {
            alert('Gagal mendapatkan lokasi. Pastikan GPS aktif.');
        },
        { enableHighAccuracy: true }
    );
}
</script>
@endpush
@endsection
