@extends('layouts.employee')

@section('page-title', 'Ajukan Cuti')

@section('content')
<div class="max-w-lg mx-auto space-y-6">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800">Ajukan Cuti</h2>
        <p class="text-gray-500 text-sm mt-1">Isi form di bawah untuk mengajukan cuti</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all">
                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">Alasan</label>
                <textarea id="reason" name="reason" rows="4" required
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all resize-none"
                          placeholder="Jelaskan alasan cuti Anda...">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('employee.leaves.index') }}"
                   class="flex-1 py-3 text-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-xl transition-all">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
