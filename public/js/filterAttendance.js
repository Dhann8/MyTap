document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById('date-filter') || document.querySelector('input[type="date"]');

    if (dateInput) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');

        const formattedToday = `${year}-${month}-${day}`;
        if (!dateInput.value) {
            dateInput.value = formattedToday;
        }
    }

    // Populate tahun dari data tabel yang ada
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

    // Jika tidak ada data dari tabel, isi dengan tahun sekarang
    const currentYear = new Date().getFullYear();
    if (years.size === 0) years.add(String(currentYear));

    // Isi dropdown tahun
    const sortedYears = Array.from(years).sort((a, b) => b - a);
    sortedYears.forEach(yr => {
        const opt = document.createElement('option');
        opt.value = yr;
        opt.textContent = yr;
        yearSelect.appendChild(opt);
    });

    // Set default ke bulan & tahun saat ini
    const now = new Date();
    yearSelect.value = String(now.getFullYear());
    if (monthSelect) monthSelect.value = String(now.getMonth() + 1).padStart(2, '0');
}

function filterAttendance() {
    const statusDropdown = document.getElementById('status-filter') || document.querySelector('select[onchange="filterAttendance()"]');
    const dateInput = document.getElementById('date-filter') || document.querySelector('input[type="date"]');
    const kelasDropdown = document.getElementById('kelas-filter');

    if (!statusDropdown || !dateInput) return;

    const selectedStatus = statusDropdown.value;
    const selectedDate = dateInput.value;
    const selectedKelas = kelasDropdown ? kelasDropdown.value : 'all';

    const rows = document.querySelectorAll('.attendance-row');

    rows.forEach(row => {
        const statusCell = row.querySelector('.status-cell');
        const dateCell = row.querySelector('.date-cell');
        const rowKelas = row.getAttribute('data-kelas') || '';

        if (statusCell && dateCell) {
            const rowStatus = statusCell.getAttribute('data-status') || statusCell.innerText.trim();
            const rowDate = dateCell.getAttribute('data-raw-date');

            const matchStatus = (selectedStatus === 'all' || rowStatus === selectedStatus);
            const matchDate = (selectedDate === '' || rowDate === selectedDate);
            const matchKelas = (selectedKelas === 'all' || rowKelas === selectedKelas);

            if (matchStatus && matchDate && matchKelas) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function exportToExcel() {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Data Absensi');
    worksheet.views = [{ showGridLines: true }];
    const rows = document.querySelectorAll("table tbody tr");

    worksheet.columns = [
        { header: 'No', key: 'no', width: 6 },
        { header: 'Nama / UID RFID', key: 'nama', width: 25 },
        { header: 'Kelas', key: 'kelas', width: 15 },
        { header: 'Tanggal', key: 'tanggal', width: 15 },
        { header: 'Jam Masuk', key: 'jam', width: 15 },
        { header: 'Status', key: 'status', width: 12 }
    ];

    let counter = 1;
    const monthSelect = document.getElementById('export-month');
    const yearSelect  = document.getElementById('export-year');
    const selectedMonth = monthSelect ? monthSelect.value : '';
    const selectedYear  = yearSelect  ? yearSelect.value  : '';

    rows.forEach(row => {
        if (row.style.display === 'none' || row.querySelector('td[colspan]')) return;

        const dateCell = row.querySelector('.date-cell');
        if (dateCell) {
            const rawDate = dateCell.getAttribute('data-raw-date') || ''; // format: YYYY-MM-DD
            const rowYear  = rawDate.substring(0, 4);
            const rowMonth = rawDate.substring(5, 7);

            if (selectedYear  && rowYear  !== selectedYear)  return;
            if (selectedMonth && rowMonth !== selectedMonth) return;
        }

        const cells = row.querySelectorAll("td");
        if (cells.length >= 6) {
            const namaClean   = cells[1].innerText.replace(/\s+/g, ' ').trim();
            const kelasClean  = cells[2].innerText.trim();
            const tanggalClean = cells[3].innerText.trim();
            const jamClean    = cells[4].innerText.trim();
            const statusClean = cells[5].innerText.trim();

            worksheet.addRow({
                no: counter++,
                nama: namaClean,
                kelas: kelasClean,
                tanggal: tanggalClean,
                jam: jamClean,
                status: statusClean
            });
        }
    });

    const headerRow = worksheet.getRow(1);
    headerRow.height = 25;
    headerRow.eachCell((cell) => {
        cell.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FFFFFF' } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: '1F497D' } };
        cell.alignment = { vertical: 'middle', horizontal: 'center' };
        cell.border = {
            top: { style: 'thin', color: { argb: 'D9D9D9' } },
            bottom: { style: 'thin', color: { argb: 'D9D9D9' } },
            left: { style: 'thin', color: { argb: 'D9D9D9' } },
            right: { style: 'thin', color: { argb: 'D9D9D9' } }
        };
    });

    worksheet.eachRow((row, rowNumber) => {
        if (rowNumber === 1) return;

        row.height = 20;

        const statusCell = row.getCell(6);
        const statusValue = statusCell.value;

        let statusFontColor = '000000';
        let statusBgColor = 'FFFFFF';

        if (statusValue === 'Hadir') { statusFontColor = '276749'; statusBgColor = 'C6F6D5'; }
        else if (statusValue === 'Sakit') { statusFontColor = '744210'; statusBgColor = 'FEFCBF'; }
        else if (statusValue === 'Izin') { statusFontColor = '2B6CB0'; statusBgColor = 'EBF8FF'; }
        else if (statusValue === 'Alpa') { statusFontColor = '9B2C2C'; statusBgColor = 'FED7D7'; }

        row.eachCell((cell, colNumber) => {
            cell.font = { name: 'Arial', size: 10 };
            cell.border = {
                top: { style: 'thin', color: { argb: 'E2E8F0' } },
                bottom: { style: 'thin', color: { argb: 'E2E8F0' } },
                left: { style: 'thin', color: { argb: 'E2E8F0' } },
                right: { style: 'thin', color: { argb: 'E2E8F0' } }
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
    workbook.xlsx.writeBuffer().then((buffer) => {
        const monthSelect = document.getElementById('export-month');
        const yearSelect  = document.getElementById('export-year');
        const selectedMonth = monthSelect ? monthSelect.value : '';
        const selectedYear  = yearSelect  ? yearSelect.value  : '';

        const bulanNames = {
            '01':'Januari','02':'Februari','03':'Maret','04':'April',
            '05':'Mei','06':'Juni','07':'Juli','08':'Agustus',
            '09':'September','10':'Oktober','11':'November','12':'Desember'
        };

        let periodLabel = '';
        if (selectedMonth && selectedYear) {
            periodLabel = `_${bulanNames[selectedMonth]}_${selectedYear}`;
        } else if (selectedMonth) {
            periodLabel = `_${bulanNames[selectedMonth]}`;
        } else if (selectedYear) {
            periodLabel = `_${selectedYear}`;
        }

        saveAs(new Blob([buffer]), `Laporan_Absensi${periodLabel}.xlsx`);
    });
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
        fetch(`${window.autocompleteUrl}?keyword=${encodeURIComponent(keyword)}&date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                dropdown.innerHTML = '';
                
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
            });
    }, 300);
}

function fetchDefaultDataByDate(date) {
    fetch(`${window.autocompleteUrl}?keyword=&date=${date}`)
        .then(response => response.json())
        .then(data => {
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

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${item.name}</div>
                <div class="text-sm text-gray-500">${item.uid}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${item.email || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${displayDate}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${displayTime}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">
                    ${item.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <a href="/attendance/${item.id}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                    Detail
                </a>
            </td>
        `;
        tableBody.appendChild(row);

        setTimeout(() => {
            row.classList.remove('bg-blue-50/50');
        }, 800);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date-filter');
    const searchInput = document.getElementById('searchInput');

    if (dateInput && searchInput) {
        dateInput.addEventListener('change', function() {
            liveSearch(searchInput.value);
        });
    }
});

document.addEventListener('click', function(e) {
    const form = document.getElementById('searchForm');
    const dropdown = document.getElementById('searchDropdown');
    if (form && !form.contains(e.target) && dropdown) {
        dropdown.classList.add('hidden');
    }
});