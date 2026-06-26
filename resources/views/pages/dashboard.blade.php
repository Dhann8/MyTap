@extends('layout.app')

@section('content')
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">Total Siswa</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">0</p>
                                <p class="mt-1 text-xs text-gray-400">Terdaftar di sistem</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>

                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">Hadir Hari Ini</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">0</p>
                                <p class="mt-1 text-xs text-gray-400 font-medium">0% kehadiran</p>
                            </div>

                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-gray-50 text-gray-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-emerald-500 to-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>

                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">Tidak Hadir</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">0</p>
                                <p class="mt-1 text-xs text-gray-400 font-medium">Sakit / Izin / Alpa</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-red-50 text-red-500 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-red-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>

                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">Kartu RFID Aktif</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">0</p>
                                <p class="mt-1 text-xs text-gray-400">Kartu terdaftar</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-violet-50 text-violet-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-violet-500 to-violet-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Absensi Terbaru</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Data absensi hari ini</p>
                            </div>
                            <span class="text-xs font-medium text-gray-400">{{ now()->format('d M Y') }}</span>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                                <svg class="w-16 h-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm font-medium text-gray-400">Belum ada data absensi</p>
                                <p class="text-xs text-gray-300 mt-1">Data akan muncul saat siswa melakukan tap RFID</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">Informasi Sistem</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Status perangkat</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection