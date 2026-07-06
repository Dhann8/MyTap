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
                                    stroke="currentColor" class="size-4 absolute right-2.5 text-gray-400 pointer-events-none">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="relative w-full h-80" id="chart-wrapper">
                                <div id="chart-loader" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none z-10">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600"></i>
                                        <span class="text-xs text-gray-500 font-semibold tracking-wide">Memuat grafik...</span>
                                    </div>
                                </div>
                                <canvas id="attendanceChart"></canvas>
                            </div>
                            <div class="relative min-h-[150px] mt-6 border-t border-gray-100 pt-6">
                                <div id="summary-loader" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex items-center justify-center transition-all duration-300 opacity-0 pointer-events-none z-10">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600"></i>
                                        <span class="text-xs text-gray-500 font-semibold tracking-wide">Memuat data tabel...</span>
                                    </div>
                                </div>
                                <div id="chartSummaryContainer">
                                    <!-- Ringkasan berupa tabel/teks akan disisipkan di sini oleh JavaScript secara dinamis -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">Informasi Sistem</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Status perangkat</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Koneksi ESP32</span>
                                <span class="flex items-center gap-1.5 text-xs font-medium text-green-600 bg-green-50 px-2.5 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                                    Online
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Database JSON</span>
                                <span class="text-xs font-medium text-gray-600 bg-gray-50 px-2.5 py-0.5 rounded-full">Aktif</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Waktu Server</span>
                                <span class="text-xs text-gray-600 font-medium">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Script Chart.js & Logika Diagram -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dates = @json($dates);
            const classes = @json($classes);
            const classDailyData = @json($classDailyData);
            const studentMonthlyData = @json($studentMonthlyData);

            const ctx = document.getElementById('attendanceChart').getContext('2d');
            let attendanceChart;

            // Memformat format tanggal label (misal: "30 Jun")
            const formatDates = dates.map(dateStr => {
                const d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });

            window.updateChartFilter = function(filterValue) {
                const chartLoader = document.getElementById('chart-loader');
                const summaryLoader = document.getElementById('summary-loader');
                
                if (chartLoader) {
                    chartLoader.classList.remove('opacity-0', 'pointer-events-none');
                    chartLoader.classList.add('opacity-100', 'pointer-events-auto');
                }
                if (summaryLoader) {
                    summaryLoader.classList.remove('opacity-0', 'pointer-events-none');
                    summaryLoader.classList.add('opacity-100', 'pointer-events-auto');
                }

                setTimeout(() => {
                    if (attendanceChart) {
                        attendanceChart.destroy();
                    }

                    let chartType = 'line';
                    let labels = [];
                    let datasets = [];

                    if (filterValue === 'all') {
                        chartType = 'line';
                        labels = formatDates;

                        const colors = [
                            { border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.05)' }, // Blue
                            { border: '#10b981', bg: 'rgba(16, 185, 129, 0.05)' }, // Emerald
                            { border: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.05)' }, // Violet
                            { border: '#f59e0b', bg: 'rgba(245, 158, 11, 0.05)' }  // Amber
                        ];

                        classes.forEach((cls, idx) => {
                            const color = colors[idx % colors.length];
                            datasets.push({
                                label: cls,
                                data: classDailyData[cls] || [],
                                borderColor: color.border,
                                backgroundColor: color.bg,
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: color.border,
                                pointHoverRadius: 6,
                                pointRadius: 2
                            });
                        });
                    } else {
                        chartType = 'bar';
                        const students = studentMonthlyData[filterValue] || [];
                        labels = students.map(s => s.name);

                        datasets.push({
                            label: 'Total Kehadiran (Hari)',
                            data: students.map(s => s.hadir),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: '#3b82f6',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            barPercentage: 0.5
                        });
                    }

                    attendanceChart = new Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        font: { family: 'Inter, sans-serif', size: 11, weight: '500' },
                                        color: '#4b5563'
                                    }
                                },
                                tooltip: {
                                    padding: 12,
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    titleFont: { family: 'Inter, sans-serif', size: 12, weight: '600' },
                                    bodyFont: { family: 'Inter, sans-serif', size: 12 },
                                    cornerRadius: 8
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: filterValue === 'all' ? undefined : 30, // Max 30 hari untuk absensi siswa
                                    ticks: {
                                        stepSize: filterValue === 'all' ? 1 : 5,
                                        color: '#9ca3af',
                                        font: { family: 'Inter, sans-serif', size: 10 }
                                    },
                                    grid: {
                                        color: '#f3f4f6'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#9ca3af',
                                        font: { family: 'Inter, sans-serif', size: 10 }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    // Perbarui Ringkasan berupa Tabel secara Dinamis
                    const summaryContainer = document.getElementById('chartSummaryContainer');
                    let summaryHtml = '';

                    // Hitung jumlah hari sekolah (exclude Sabtu & Minggu) dalam 30 hari terakhir
                    const schoolDaysCount = dates.filter(dateStr => {
                        const d = new Date(dateStr);
                        return d.getDay() !== 0 && d.getDay() !== 6;
                    }).length;

                    if (filterValue === 'all') {
                        summaryHtml = `
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Ringkasan Kehadiran Per Kelas</h4>
                            <div class="overflow-x-auto rounded-lg border border-gray-100">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Nama Kelas</th>
                                            <th scope="col" class="px-4 py-3 text-center">Jumlah Siswa</th>
                                            <th scope="col" class="px-4 py-3 text-center">Total Kehadiran</th>
                                            <th scope="col" class="px-4 py-3 class text-center">Rata-rata Kehadiran Harian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                        `;

                        classes.forEach(cls => {
                            const dailyData = classDailyData[cls] || [];
                            const totalPresent = dailyData.reduce((sum, val) => sum + val, 0);
                            const studentsCount = studentMonthlyData[cls] ? studentMonthlyData[cls].length : 0;
                            const dailyAvg = schoolDaysCount > 0 ? (totalPresent / schoolDaysCount).toFixed(1) : 0;

                            summaryHtml += `
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 font-semibold text-gray-900">${cls}</td>
                                    <td class="px-4 py-3 text-center">${studentsCount} siswa</td>
                                    <td class="px-4 py-3 text-center font-medium text-blue-600">${totalPresent} kali tap</td>
                                    <td class="px-4 py-3 text-center text-green-600 font-semibold">${dailyAvg} siswa/hari</td>
                                </tr>
                            `;
                        });

                        summaryHtml += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        summaryHtml = `
                            <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 gap-2">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Rincian Siswa Kelas ${filterValue}</h4>
                                <span class="text-xs text-gray-400 font-medium">Berdasarkan data 30 hari terakhir (terdapat ${schoolDaysCount} hari sekolah)</span>
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-gray-100">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Nama Siswa</th>
                                            <th scope="col" class="px-4 py-3 text-center text-green-600">Hadir</th>
                                            <th scope="col" class="px-4 py-3 text-center text-yellow-600">Sakit</th>
                                            <th scope="col" class="px-4 py-3 text-center text-blue-600">Izin</th>
                                            <th scope="col" class="px-4 py-3 text-center text-red-600">Alpa</th>
                                            <th scope="col" class="px-4 py-3 text-center">Persentase Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                        `;

                        const students = studentMonthlyData[filterValue] || [];
                        students.forEach(s => {
                            const presenceRate = schoolDaysCount > 0 ? Math.round((s.hadir / schoolDaysCount) * 100) : 0;
                            let rateColor = 'text-red-600 bg-red-50';
                            if (presenceRate >= 85) rateColor = 'text-green-600 bg-green-50';
                            else if (presenceRate >= 75) rateColor = 'text-yellow-600 bg-yellow-50';

                            summaryHtml += `
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 font-semibold text-gray-900">${s.name}</td>
                                    <td class="px-4 py-3 text-center text-green-600 font-semibold">${s.hadir} hari</td>
                                    <td class="px-4 py-3 text-center text-yellow-600 font-semibold">${s.sakit} hari</td>
                                    <td class="px-4 py-3 text-center text-blue-600 font-semibold">${s.izin} hari</td>
                                    <td class="px-4 py-3 text-center text-red-600 font-semibold">${s.alpa} hari</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold ${rateColor}">${presenceRate}%</span>
                                    </td>
                                </tr>
                            `;
                        });

                        summaryHtml += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }

                    summaryContainer.innerHTML = summaryHtml;

                    if (chartLoader) {
                        chartLoader.classList.remove('opacity-100', 'pointer-events-auto');
                        chartLoader.classList.add('opacity-0', 'pointer-events-none');
                    }
                    if (summaryLoader) {
                        summaryLoader.classList.remove('opacity-100', 'pointer-events-auto');
                        summaryLoader.classList.add('opacity-0', 'pointer-events-none');
                    }
                }, 400);
            };

            updateChartFilter('all');
        });
    </script>
@endsection