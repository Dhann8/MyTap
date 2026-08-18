    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        let attendanceChart;
        const formatDates = dates.map(dateStr => {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });

        window.updateChartFilter = function (filterValue) {
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
                        { border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.05)' },
                        { border: '#10b981', bg: 'rgba(16, 185, 129, 0.05)' },
                        { border: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.05)' },
                        { border: '#f59e0b', bg: 'rgba(245, 158, 11, 0.05)' }
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
                                max: filterValue === 'all' ? undefined : 30,
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

                // Hitung jumlah hari sekolah (exclude Sabtu & Minggu)
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
                                        <th scope="col" class="px-4 py-3 text-center">Rata-rata Kehadiran Harian</th>
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

                if (summaryContainer) {
                    summaryContainer.innerHTML = summaryHtml;
                }

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

        // Render chart default pertama kali
        updateChartFilter('all');

        // ==========================================
        // 2. MONITORING REALTIME SERVER WHATSAPP
        // ==========================================
        function checkWaStatus() {
            const statusBadge = document.getElementById('wa-status-badge');
            const statusDot = document.getElementById('wa-status-dot');
            const statusText = document.getElementById('wa-status-text');
            const queueCount = document.getElementById('wa-queue-count');
            const sentCount = document.getElementById('wa-sent-count');

            // Cek jika elemen status WA ada di halaman
            if (!statusBadge) return;

            fetch(window.waStatusUrl || "/wa/status-json")
                .then(response => {
                    if (!response.ok) throw new Error('Server Offline');
                    return response.json();
                })
                .then(res => {
                    if (res.status && res.data) {
                        const isReady = res.data.isReady;

                        // Perbarui Badge & Dot
                        if (isReady) {
                            statusBadge.className = "flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full text-green-600 bg-green-50";
                            if (statusDot) statusDot.className = "w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse";
                            if (statusText) statusText.innerText = "Online & Siap";
                        } else {
                            statusBadge.className = "flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full text-amber-600 bg-amber-50";
                            if (statusDot) statusDot.className = "w-1.5 h-1.5 rounded-full bg-amber-600";
                            if (statusText) statusText.innerText = "Terputus / Reconnect";
                        }

                        // Perbarui Jumlah Antrean & Terkirim
                        if (res.data.stats) {
                            if (queueCount) queueCount.innerText = res.data.stats.wait || 0;
                            if (sentCount) sentCount.innerText = res.data.stats.completed || 0;
                        }
                    }
                })
                .catch(() => {
                    // Jika Node.js mati / rute gagal diakses
                    if (statusBadge) {
                        statusBadge.className = "flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full text-red-600 bg-red-50";
                    }
                    if (statusDot) statusDot.className = "w-1.5 h-1.5 rounded-full bg-red-600";
                    if (statusText) statusText.innerText = "Offline (Server Down)";
                });
        }

        // Jalankan pemeriksaan status pertama kali dan ulang setiap 5 detik
        checkWaStatus();
        setInterval(checkWaStatus, 5000);
    });