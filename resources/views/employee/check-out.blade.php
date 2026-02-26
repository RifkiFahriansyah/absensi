@extends('layouts.employee')

@section('page-title', 'Check Out')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800">Check Out</h2>
        <p class="text-gray-500 text-sm mt-1">Ambil foto selfie untuk absensi pulang</p>
    </div>

    {{-- Check In Info --}}
    <div class="bg-primary-50 rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
        </div>
        <div>
            <p class="text-xs text-primary-600 font-medium">Check In Hari Ini</p>
            <p class="text-sm font-bold text-primary-800">{{ $attendance->check_in }}</p>
        </div>
    </div>

    <form id="checkout-form" method="POST" action="{{ route('employee.check-out.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="photo" id="photo-data">

        {{-- Camera Preview --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="relative">
                <video id="camera-preview" class="w-full aspect-[4/3] object-cover bg-gray-900" autoplay playsinline></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
                <img id="photo-preview" class="w-full aspect-[4/3] object-cover hidden">

                <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                    <button type="button" id="btn-capture" onclick="capturePhoto()"
                            class="w-16 h-16 bg-white rounded-full shadow-xl flex items-center justify-center hover:scale-105 transition-transform border-4 border-blue-500">
                        <div class="w-12 h-12 bg-blue-500 rounded-full"></div>
                    </button>
                </div>
                <div id="btn-retake-wrap" class="absolute top-4 right-4 hidden">
                    <button type="button" onclick="retakePhoto()"
                            class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-xl text-sm font-medium text-gray-700 shadow-lg hover:bg-white transition-all">
                        Ulangi Foto
                    </button>
                </div>
            </div>
        </div>

        {{-- GPS Status --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3">
                <div id="gps-indicator" class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800" id="gps-status">Mendapatkan lokasi...</p>
                    <p class="text-xs text-gray-500" id="gps-coords">Mohon izinkan akses lokasi</p>
                </div>
            </div>
        </div>

        <button type="submit" id="btn-submit" disabled
                class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-blue-500/25 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:-translate-y-0.5 disabled:hover:translate-y-0">
            <span id="submit-text">Check Out Sekarang</span>
            <span id="submit-loading" class="hidden">Memproses...</span>
        </button>

        <a href="{{ route('employee.dashboard') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            ← Kembali ke Dashboard
        </a>
    </form>
</div>

@push('scripts')
<script>
let stream = null;
let photoTaken = false;
let gpsReady = false;

async function initCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false
        });
        document.getElementById('camera-preview').srcObject = stream;
    } catch (err) {
        alert('Gagal mengakses kamera.');
    }
}

function capturePhoto() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('camera-canvas');
    const preview = document.getElementById('photo-preview');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('photo-data').value = dataUrl;

    preview.src = dataUrl;
    preview.classList.remove('hidden');
    video.classList.add('hidden');
    document.getElementById('btn-capture').classList.add('hidden');
    document.getElementById('btn-retake-wrap').classList.remove('hidden');

    photoTaken = true;
    checkReady();
}

function retakePhoto() {
    const video = document.getElementById('camera-preview');
    const preview = document.getElementById('photo-preview');

    preview.classList.add('hidden');
    video.classList.remove('hidden');
    document.getElementById('btn-capture').classList.remove('hidden');
    document.getElementById('btn-retake-wrap').classList.add('hidden');
    document.getElementById('photo-data').value = '';

    photoTaken = false;
    checkReady();
}

function getLocation() {
    if (!navigator.geolocation) {
        document.getElementById('gps-status').textContent = 'GPS tidak didukung';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('gps-status').textContent = 'Lokasi ditemukan';
            document.getElementById('gps-coords').textContent = `${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;

            const indicator = document.getElementById('gps-indicator');
            indicator.classList.remove('bg-gray-100');
            indicator.classList.add('bg-blue-100');
            indicator.innerHTML = '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';

            gpsReady = true;
            checkReady();
        },
        function(error) {
            document.getElementById('gps-status').textContent = 'Gagal mendapatkan lokasi';
            document.getElementById('gps-coords').textContent = 'Pastikan GPS aktif';
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

function checkReady() {
    document.getElementById('btn-submit').disabled = !(photoTaken && gpsReady);
}

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    if (!photoTaken || !gpsReady) { e.preventDefault(); return; }
    document.getElementById('submit-text').classList.add('hidden');
    document.getElementById('submit-loading').classList.remove('hidden');
    document.getElementById('btn-submit').disabled = true;
});

initCamera();
getLocation();
</script>
@endpush
@endsection
