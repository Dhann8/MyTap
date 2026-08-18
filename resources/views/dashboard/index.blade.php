@extends('layout.app')

@section('content')
    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">Total Siswa</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $totalSiswa }}</p>
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
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $hadirHariIni }}</p>
                                <p class="mt-1 text-xs text-gray-400 font-medium">{{ $persenKehadiran }}% kehadiran</p>
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
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $tidakHadir }}</p>
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
                                <p class="text-xl font-bold text-gray-900">RFID Aktif</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $rfidAktif }}</p>
                                <p class="mt-1 text-xs text-gray-400">Kartu aktif</p>
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

                    <div
                        class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xl font-bold text-gray-900">RFID Nonaktif</p>
                                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $rfidTidakAktif }}</p>
                                <p class="mt-1 text-xs text-gray-400">Kartu dinonaktifkan</p>
                            </div>
                            <div
                                class="flex items-center justify-center w-11 h-11 rounded-xl bg-orange-50 text-orange-500 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-orange-400 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Diagram Kehadiran Bulanan</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Tren kehadiran 30 hari terakhir</p>
                            </div>

                            <div class="relative flex items-center max-w-xs">
                                <select id="class-chart-filter" onchange="updateChartFilter(this.value)"
                                    class="pl-3 pr-8 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none cursor-pointer">
                                    <option value="all">Semua Kelas</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls }}">{{ $cls }}</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor"
                                    class="size-4 absolute right-2.5 text-gray-400 pointer-events-none">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="relative w-full h-80" id="chart-wrapper">
                                <div id="chart-loader"
                                    class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none z-10">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600"></i>
                                        <span class="text-xs text-gray-500 font-semibold tracking-wide">Memuat
                                            grafik...</span>
                                    </div>
                                </div>
                                <canvas id="attendanceChart"></canvas>
                            </div>
                            <div class="relative min-h-[150px] mt-6 border-t border-gray-100 pt-6">
                                <div id="summary-loader"
                                    class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none z-10">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600"></i>
                                        <span class="text-xs text-gray-500 font-semibold tracking-wide">Memuat data
                                            tabel...</span>
                                    </div>
                                </div>
                                <div id="chartSummaryContainer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Status Sistem Absensi</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Indikator ketersediaan layanan</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 {{ ($data['isReady'] ?? false) ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">WhatsApp Gateway</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            @if($data['isReady'] ?? false)
                                                <span class="text-green-600">Online</span> & Siap
                                            @else
                                                <span class="text-red-600">Offline</span> / Terputus
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Perangkat Scan RFID</p>
                                        <p class="text-sm font-semibold text-green-600">Standby & Aktif</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Server Aplikasi</p>
                                        <p class="text-sm font-semibold text-green-600">Berjalan Normal</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pb-1">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400">Koneksi Database</p>
                                        <p class="text-sm font-semibold text-green-600">Terhubung</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    window.dashboardData = {
        dates: @json($dates),
        classes: @json($classes),
        classDailyData: @json($classDailyData),
        studentMonthlyData: @json($studentMonthlyData)
    };
    window.waStatusUrl = "{{ route('wa.status-json') }}";
</script>
    <script src="{{ asset('js/Dashbord.js') }}"></script>
@endsection