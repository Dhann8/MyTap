@extends('layout.app')
@section('content')

    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64 bg-gray-50">
            <x-Header />

            <div class="p-8">
                <div class="mb-6">
                    <a href="{{ route('attendance.index') }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 flex items-center gap-2 font-medium">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Absensi
                    </a>
                </div>

                <div class="max-w-2xl bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-blue-600 px-6 py-8 text-white">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-3xl font-bold text-white">
                                {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">{{ $attendance->user->name }}</h1>
                                <p class="text-blue-100 text-sm">UID RFID: {{ $attendance->user->uid }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $attendance->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</label>
                                <p class="mt-1 text-sm font-medium text-gray-900 capitalize">{{ $attendance->user->role }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Absen</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Masuk</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $attendance->time_in ? substr($attendance->time_in, 0, 5) . ' WIB' : '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Kehadiran</label>
                            <span class="px-3 py-1.5 text-sm font-semibold rounded-full {{ 
                                $attendance->status === 'Hadir' ? 'bg-green-100 text-green-800' :
                                ($attendance->status === 'Sakit' ? 'bg-yellow-100 text-yellow-800' :
                                ($attendance->status === 'Izin' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $attendance->status }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 pt-6 flex justify-end">
                            <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus Data Absensi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
