document.addEventListener("DOMContentLoaded", function () {
    populateExportYears();

    filterAttendance();
});

function populateExportYears() {
    const yearSelect = document.getElementById('export-year');
    const monthSelect = document.getElementById('export-month');
    if (!yearSelect) return;

    const rows = document.querySelectorAll('.attendance-row');
    const years = new Set();

    rows.forEach(row => {
        const dateCell = row.querySelector('.date-cell');
        if (dateCell) {
            const rawDate = dateCell.getAttribute('data-raw-date') || '';
            const year = rawDate.substring(0, 4);
            if (year) years.add(year);
        }
    });

    const currentYear = new Date().getFullYear();
    if (years.size === 0) years.add(String(currentYear));

    const sortedYears = Array.from(years).sort((a, b) => b - a);
    sortedYears.forEach(yr => {
        const opt = document.createElement('option');
        opt.value = yr;
        opt.textContent = yr;
        yearSelect.appendChild(opt);
    });

    const now = new Date();
    yearSelect.value = String(now.getFullYear());
    if (monthSelect) monthSelect.value = String(now.getMonth() + 1).padStart(2, '0');
}

function filterAttendance() {
    const tableLoader = document.getElementById('table-loader');
    if (tableLoader) {
        tableLoader.classList.remove('opacity-0', 'pointer-events-none');
        tableLoader.classList.add('opacity-100', 'pointer-events-auto');
    }

    setTimeout(() => {
        const statusDropdown = document.getElementById('status-filter');
        const kelasDropdown  = document.getElementById('kelas-filter');

        const selectedStatus = statusDropdown ? statusDropdown.value : 'all';
        const selectedKelas  = kelasDropdown  ? kelasDropdown.value  : 'all';

        const rows = document.querySelectorAll('.attendance-row');

        rows.forEach(row => {
            const statusCell = row.querySelector('.status-cell');
            const rowKelas   = row.getAttribute('data-kelas') || '';

            const rowStatus  = statusCell ? (statusCell.getAttribute('data-status') || statusCell.innerText.trim()) : '';

            const matchStatus = (selectedStatus === 'all' || rowStatus === selectedStatus);
            const matchKelas  = (selectedKelas  === 'all' || rowKelas  === selectedKelas);

            row.style.display = (matchStatus && matchKelas) ? '' : 'none';
        });

        renumberRows();

        if (tableLoader) {
            tableLoader.classList.remove('opacity-100', 'pointer-events-auto');
            tableLoader.classList.add('opacity-0', 'pointer-events-none');
        }
    }, 250);
}

function renumberRows() {
    const rows = document.querySelectorAll('.attendance-row');
    let counter = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) firstCell.textContent = counter++;
        }
    });
}

async function exportToExcel() {
    const monthSelect = document.getElementById('export-month');
    const yearSelect  = document.getElementById('export-year');
    const selectedMonth = monthSelect ? monthSelect.value : '';
    const selectedYear  = yearSelect  ? yearSelect.value  : '';

    const bulanNames = {
        '01':'Januari','02':'Februari','03':'Maret','04':'April',
        '05':'Mei','06':'Juni','07':'Juli','08':'Agustus',
        '09':'September','10':'Oktober','11':'November','12':'Desember'
    };

    const url = `${window.allDataUrl}?month=${selectedMonth}&year=${selectedYear}`;
    
    try {
        const response = await fetch(url);
        const attendances = await response.json();

        if (!attendances || attendances.length === 0) {
            alert("Tidak ada data absensi untuk periode yang dipilih.");
            return;
        }

        const workbook = new ExcelJS.Workbook();
        const groupedData = {};

        const monthNames = {
            '01':'Januari','02':'Februari','03':'Maret','04':'April',
            '05':'Mei','06':'Juni','07':'Juli','08':'Agustus',
            '09':'September','10':'Oktober','11':'November','12':'Desember'
        };

        attendances.forEach(att => {
            const rawDate = att.date || '';
            let sheetKey  = 'Data Absensi'; 
            let sheetName = 'Data Absensi'; 

            if (rawDate) {
                const parts    = rawDate.split('-');
                const rowYear  = parts[0];
                const rowMonth = parts[1];
                const rowDay   = parseInt(parts[2], 10);

                if (selectedMonth) {
                    sheetKey  = rawDate;
                    sheetName = `${rowDay} ${monthNames[rowMonth] || rowMonth} ${rowYear}`;
                } else {
                    sheetKey  = `${rowYear}-${rowMonth}`;
                    sheetName = `${monthNames[rowMonth] || rowMonth} ${rowYear}`;
                }
            }

            if (!groupedData[sheetKey]) {
                groupedData[sheetKey] = { name: sheetName, rows: [] };
            }

            groupedData[sheetKey].rows.push({
                nama   : att.user?.name  || att.name  || '-',
                kelas  : att.user?.kelas || att.kelas || '-',
                tanggal: rawDate,
                jam    : att.time_in || att.jam || '-',
                status : att.status  || 'Hadir'
            });
        });

        const sortedKeys = Object.keys(groupedData).sort();

        sortedKeys.forEach(sheetKey => {
            const { name: sheetName, rows: sheetRows } = groupedData[sheetKey];
            const worksheet = workbook.addWorksheet(sheetName);
            worksheet.views = [{ showGridLines: true }];

            worksheet.columns = [
                { header: 'No', key: 'no', width: 6 },
                { header: 'Nama', key: 'nama', width: 25 },
                { header: 'Kelas', key: 'kelas', width: 15 },
                { header: 'Tanggal', key: 'tanggal', width: 15 },
                { header: 'Jam Masuk', key: 'jam', width: 15 },
                { header: 'Status', key: 'status', width: 12 }
            ];

            let counter = 1;
            sheetRows.forEach(item => {
                worksheet.addRow({
                    no: counter++,
                    nama: item.nama,
                    kelas: item.kelas,
                    tanggal: item.tanggal,
                    jam: item.jam,
                    status: item.status
                });
            });

            const headerRow = worksheet.getRow(1);
            headerRow.height = 25;
            headerRow.eachCell((cell) => {
                cell.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FFFFFF' } };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: '1F497D' } };
                cell.alignment = { vertical: 'middle', horizontal: 'center' };
                cell.border = {
                    top: { style: 'thin', color: { argb: 'D9D9D9' } }, bottom: { style: 'thin', color: { argb: 'D9D9D9' } },
                    left: { style: 'thin', color: { argb: 'D9D9D9' } }, right: { style: 'thin', color: { argb: 'D9D9D9' } }
                };
            });

            worksheet.eachRow((row, rowNumber) => {
                if (rowNumber === 1) return;
                row.height = 20;

                const statusCell = row.getCell(6);
                const statusValue = statusCell.value;
                let statusFontColor = '000000'; let statusBgColor = 'FFFFFF';

                if (statusValue === 'Hadir') { statusFontColor = '276749'; statusBgColor = 'C6F6D5'; }
                else if (statusValue === 'Sakit') { statusFontColor = '744210'; statusBgColor = 'FEFCBF'; }
                else if (statusValue === 'Izin') { statusFontColor = '2B6CB0'; statusBgColor = 'EBF8FF'; }
                else if (statusValue === 'Alpa') { statusFontColor = '9B2C2C'; statusBgColor = 'FED7D7'; }

                row.eachCell((cell, colNumber) => {
                    cell.font = { name: 'Arial', size: 10 };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'E2E8F0' } }, bottom: { style: 'thin', color: { argb: 'E2E8F0' } },
                        left: { style: 'thin', color: { argb: 'E2E8F0' } }, right: { style: 'thin', color: { argb: 'E2E8F0' } }
                    };

                    if (rowNumber % 2 === 0) {
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'F7FAFC' } };
                    }

                    if (colNumber === 1 || colNumber === 4 || colNumber === 5) {
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                    } else if (colNumber === 6) {
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: statusBgColor } };
                        cell.font = { name: 'Arial', size: 10, bold: true, color: { argb: statusFontColor } };
                        cell.alignment = { vertical: 'middle', horizontal: 'center' };
                    } else {
                        cell.alignment = { vertical: 'middle', horizontal: 'left' };
                    }
                });
            });
        });

        const buffer = await workbook.xlsx.writeBuffer();
        let periodLabel = '';
        if (selectedMonth && selectedYear) periodLabel = `_${bulanNames[selectedMonth]}_${selectedYear}`;
        else if (selectedMonth) periodLabel = `_${bulanNames[selectedMonth]}`;
        else if (selectedYear) periodLabel = `_${selectedYear}`;

        saveAs(new Blob([buffer]), `Laporan_Absensi${periodLabel}.xlsx`);

    } catch (error) {
        console.error("Export Error:", error);
        alert("Terjadi kesalahan saat mengambil data dari server.");
    }
}
let debounceTimer;

function liveSearch(keyword) {
    const dropdown = document.getElementById('searchDropdown');
    const tableBody = document.getElementById('attendance-table-body');
    const dateInput = document.getElementById('date-filter'); 
    const selectedDate = dateInput ? dateInput.value : '';
    
    if (keyword.trim().length < 2) {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
        
        fetchDefaultDataByDate(selectedDate);
        return;
    }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const tableLoader = document.getElementById('table-loader');
        if (tableLoader) {
            tableLoader.classList.remove('opacity-0', 'pointer-events-none');
            tableLoader.classList.add('opacity-100', 'pointer-events-auto');
        }

        const startTime = Date.now();

        fetch(`${window.autocompleteUrl}?keyword=${encodeURIComponent(keyword)}&date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                dropdown.innerHTML = '';
                
                const elapsedTime = Date.now() - startTime;
                const delay = Math.max(0, 300 - elapsedTime);

                setTimeout(() => {
                    if (data.length > 0) {
                        dropdown.classList.remove('hidden');
                        updateTableContent(data); 
                        
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-700 flex justify-between";
                            
                            div.innerHTML = `
                                <span class="font-medium text-gray-900">${item.name}</span>
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">UID: ${item.uid}</span>
                            `;
                            
                            div.onclick = function() {
                                document.getElementById('searchInput').value = item.name;
                                dropdown.classList.add('hidden');
                                updateTableContent([item]); 
                            };
                            
                            dropdown.appendChild(div);
                        });
                    } else {
                        dropdown.classList.remove('hidden');
                        dropdown.innerHTML = `<div class="px-4 py-2 text-sm text-gray-500 italic">Tidak ditemukan hasil...</div>`;
                        
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada data absensi ditemukan pada tanggal ini.
                                </td>
                            </tr>`;
                    }

                    if (tableLoader) {
                        tableLoader.classList.remove('opacity-100', 'pointer-events-auto');
                        tableLoader.classList.add('opacity-0', 'pointer-events-none');
                    }
                }, delay);
            })
            .catch(() => {
                if (tableLoader) {
                    tableLoader.classList.remove('opacity-100', 'pointer-events-auto');
                    tableLoader.classList.add('opacity-0', 'pointer-events-none');
                }
            });
    }, 300);
}

function fetchDefaultDataByDate(date) {
    const tableLoader = document.getElementById('table-loader');
    if (tableLoader) {
        tableLoader.classList.remove('opacity-0', 'pointer-events-none');
        tableLoader.classList.add('opacity-100', 'pointer-events-auto');
    }

    const startTime = Date.now();

    fetch(`${window.autocompleteUrl}?keyword=&date=${date}`)
        .then(response => response.json())
        .then(data => {
            const elapsedTime = Date.now() - startTime;
            const delay = Math.max(0, 300 - elapsedTime);

            setTimeout(() => {
                if (data.length > 0) {
                    updateTableContent(data);
                } else {
                    document.getElementById('attendance-table-body').innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Tidak ada data absensi pada tanggal ini.
                            </td>
                        </tr>`;
                }

                if (tableLoader) {
                    tableLoader.classList.remove('opacity-100', 'pointer-events-auto');
                    tableLoader.classList.add('opacity-0', 'pointer-events-none');
                }
            }, delay);
        })
        .catch(() => {
            if (tableLoader) {
                tableLoader.classList.remove('opacity-100', 'pointer-events-auto');
                tableLoader.classList.add('opacity-0', 'pointer-events-none');
            }
        });
}
function updateTableContent(items) {
    const tableBody = document.getElementById('attendance-table-body');
    tableBody.innerHTML = ''; 

    items.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = "hover:bg-gray-50 transition-colors duration-200 attendance-row bg-blue-50/50"; 
        
        let statusClass = 'bg-red-100 text-red-800';
        if (item.status === 'Hadir') statusClass = 'bg-green-100 text-green-800';
        else if (item.status === 'Sakit') statusClass = 'bg-yellow-100 text-yellow-800';
        else if (item.status === 'Izin') statusClass = 'bg-blue-100 text-blue-800';

        const displayTime = item.time_in ? item.time_in.substring(0, 5) + ' WIB' : '-';
        const displayDate = item.date_formatted ? item.date_formatted : item.date;

        const actionHtml = `
            <a href="/attendance/${item.id}" 
               class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 transition-all duration-200"
               title="Detail Absensi">
                <i class="fa-solid fa-eye text-sm"></i>
            </a>
            <form action="/attendance/${item.id}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')" style="display:inline;">
                <input type="hidden" name="_token" value="${window.csrfToken || ''}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" 
                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 transition-all duration-200"
                        title="Hapus Absensi">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            </form>
        `;

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${item.name}</div>
                <div class="text-sm text-gray-500">${item.uid}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${item.kelas || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${displayDate}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${displayTime}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">
                    ${item.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-2">
                ${actionHtml}
            </td>
        `;
        tableBody.appendChild(row);

        setTimeout(() => {
            row.classList.remove('bg-blue-50/50');
        }, 800);
    });
}
document.addEventListener('click', function(e) {
    const form = document.getElementById('searchForm');
    const dropdown = document.getElementById('searchDropdown');
    if (form && !form.contains(e.target) && dropdown) {
        dropdown.classList.add('hidden');
    }
});