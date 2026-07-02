
function updateRfidStatus(userId, newStatus, selectEl) {
    if (newStatus === 'active') {
        selectEl.className = "px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-green-50 text-green-800 cursor-pointer";
    } else {
        selectEl.className = "px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-red-50 text-red-800 cursor-pointer";
    }

    fetch(`/users/${userId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken || ''
        },
        body: JSON.stringify({ rfid_status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Gagal memperbarui status RFID: ' + data.message);
            location.reload();
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan koneksi saat mengubah status.');
        location.reload();
    });
}

let debounceTimer;
const classesList = [];

function initClassesList() {
    const majors = [
        { name: 'rpl', count: 10 },
        { name: 'dkv', count: 3 }
    ];
    const grades = ['X', 'XI', 'XII'];

    grades.forEach(grade => {
        majors.forEach(major => {
            for (let i = 1; i <= major.count; i++) {
                classesList.push(`${grade}-${major.name} ${i}`);
            }
        });
    });
}

function initKelasAutocomplete() {
    const kelasInput = document.getElementById('filter-kelas-search');
    const suggestionsBox = document.getElementById('filter-kelas-suggestions');

    if (!kelasInput || !suggestionsBox) return;

    kelasInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().replace(/[\s-]/g, '');
        suggestionsBox.innerHTML = '';
        
        if (!query) {
            suggestionsBox.classList.add('hidden');
            return;
        }

        const matches = classesList.filter(item => {
            const normalizedItem = item.toLowerCase().replace(/[\s-]/g, '');
            return normalizedItem.includes(query);
        });

        if (matches.length === 0) {
            suggestionsBox.classList.add('hidden');
            return;
        }

        matches.forEach(match => {
            const row = document.createElement('div');
            row.className = "px-3 py-1.5 text-xs text-gray-700 hover:bg-blue-50 cursor-pointer transition-colors duration-150";
            row.textContent = match;
            row.addEventListener('click', function() {
                kelasInput.value = match;
                suggestionsBox.classList.add('hidden');
            });
            suggestionsBox.appendChild(row);
        });

        suggestionsBox.classList.remove('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!kelasInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.classList.add('hidden');
        }
    });

    kelasInput.addEventListener('focus', function() {
        if (this.value) {
            const event = new Event('input');
            kelasInput.dispatchEvent(event);
        }
    });
}

function liveSearchUsers(keyword) {
    const dropdown = document.getElementById('searchDropdown');
    
    if (keyword.trim().length < 2) {
        if (dropdown) {
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
        }
        fetchFilteredData();
        return;
    }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        const tableLoader = document.getElementById('table-loader');
        if (tableLoader) {
            tableLoader.classList.remove('opacity-0', 'pointer-events-none');
            tableLoader.classList.add('opacity-100', 'pointer-events-auto');
        }

        const angkatan = document.querySelector('input[name="filter-angkatan"]:checked')?.value || 'all';
        const jurusan  = document.querySelector('input[name="filter-jurusan"]:checked')?.value || 'all';
        const kelas    = document.getElementById('filter-kelas-search')?.value || '';
        const role     = document.querySelector('input[name="filter-role"]:checked')?.value || 'all';

        const url = `${window.autocompleteUrl}?keyword=${encodeURIComponent(keyword)}&angkatan=${angkatan}&jurusan=${jurusan}&kelas=${encodeURIComponent(kelas)}&role=${role}`;

        const startTime = Date.now();

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (dropdown) {
                    dropdown.innerHTML = '';
                    if (data.length > 0) {
                        dropdown.classList.remove('hidden');
                        data.slice(0, 5).forEach(item => {
                            const div = document.createElement('div');
                            div.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-700 flex justify-between";
                            div.innerHTML = `
                                <span class="font-medium text-gray-900">${item.name}</span>
                                <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">UID: ${item.uid}</span>
                            `;
                            div.onclick = function() {
                                const searchInput = document.getElementById('searchInput');
                                if (searchInput) searchInput.value = item.name;
                                dropdown.classList.add('hidden');
                                updateTableContent([item]);
                            };
                            dropdown.appendChild(div);
                        });
                    } else {
                        dropdown.classList.add('hidden');
                    }
                }

                const elapsedTime = Date.now() - startTime;
                const delay = Math.max(0, 300 - elapsedTime);
                setTimeout(() => {
                    updateTableContent(data);
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

function fetchFilteredData() {
    const tableLoader = document.getElementById('table-loader');
    if (tableLoader) {
        tableLoader.classList.remove('opacity-0', 'pointer-events-none');
        tableLoader.classList.add('opacity-100', 'pointer-events-auto');
    }

    const keyword  = document.getElementById('searchInput')?.value || '';
    const angkatan = document.querySelector('input[name="filter-angkatan"]:checked')?.value || 'all';
    const jurusan  = document.querySelector('input[name="filter-jurusan"]:checked')?.value || 'all';
    const kelas    = document.getElementById('filter-kelas-search')?.value || '';
    const role     = document.querySelector('input[name="filter-role"]:checked')?.value || 'all';

    const url = `${window.autocompleteUrl}?keyword=${encodeURIComponent(keyword)}&angkatan=${angkatan}&jurusan=${jurusan}&kelas=${encodeURIComponent(kelas)}&role=${role}`;

    const startTime = Date.now();

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const elapsedTime = Date.now() - startTime;
            const delay = Math.max(0, 300 - elapsedTime);
            setTimeout(() => {
                updateTableContent(data);
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
    const tableBody = document.querySelector('table tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';

    const hasFilter = checkHasActiveFilters();
    const resetBtn = document.getElementById('reset-filter-btn');
    if (resetBtn) {
        resetBtn.classList.toggle('hidden', !hasFilter);
    }

    const paginator = document.getElementById('pagination-container');
    if (paginator) {
        paginator.style.display = hasFilter ? 'none' : 'block';
    }

    if (items.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                    Tidak ada data user ditemukan.
                </td>
            </tr>`;
        return;
    }

    items.forEach((u, index) => {
        const row = document.createElement('tr');
        row.className = "hover:bg-gray-50 transition-colors duration-200 user-row bg-blue-50/20";

        const badgeClass = u.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
        const roleLabel = u.role.charAt(0).toUpperCase() + u.role.slice(1);
        
        const isSelf = String(window.authUserId) === String(u.id);
        const actionHtml = `
            <a href="/users/${u.id}/edit" 
               class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-800 transition-all duration-200"
               title="Edit User">
                <i class="fa-solid fa-pen-to-square text-sm"></i>
            </a>
            ${!isSelf ? `
                <form action="/users/${u.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Semua data absen terkait juga akan hilang.')" style="display:inline;">
                    <input type="hidden" name="_token" value="${window.csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" 
                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 transition-all duration-200"
                            title="Hapus User">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </form>
            ` : ''}
        `;

        const rfidStatus = u.rfid_status || 'active';
        const statusSelectClass = rfidStatus === 'active' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800';

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${u.name}</div>
                <div class="text-xs text-gray-500">${u.email}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">${u.uid || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                    ${roleLabel}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${u.kelas || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <select onchange="updateRfidStatus(${u.id}, this.value, this)" 
                    class="px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-150 cursor-pointer ${statusSelectClass}">
                    <option value="active" ${rfidStatus === 'active' ? 'selected' : ''}>Aktif</option>
                    <option value="inactive" ${rfidStatus === 'inactive' ? 'selected' : ''}>Nonaktif</option>
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-2">
                ${actionHtml}
            </td>
        `;
        tableBody.appendChild(row);

        setTimeout(() => {
            row.classList.remove('bg-blue-50/20');
        }, 800);
    });
}

function checkHasActiveFilters() {
    const keyword  = document.getElementById('searchInput')?.value || '';
    const angkatan = document.querySelector('input[name="filter-angkatan"]:checked')?.value || 'all';
    const jurusan  = document.querySelector('input[name="filter-jurusan"]:checked')?.value || 'all';
    const kelas    = document.getElementById('filter-kelas-search')?.value || '';
    const role     = document.querySelector('input[name="filter-role"]:checked')?.value || 'all';

    return keyword !== '' || angkatan !== 'all' || jurusan !== 'all' || kelas !== '' || role !== 'all';
}

function applyFilters() {
    fetchFilteredData();
    const card = document.getElementById('filter-dropdown-card');
    if (card) card.classList.add('hidden');
}

function resetFilters() {
    const angkatanAll = document.querySelector('input[name="filter-angkatan"][value="all"]');
    const jurusanAll  = document.querySelector('input[name="filter-jurusan"][value="all"]');
    const roleAll     = document.querySelector('input[name="filter-role"][value="all"]');
    const kelasInput  = document.getElementById('filter-kelas-search');

    if (angkatanAll) angkatanAll.checked = true;
    if (jurusanAll)  jurusanAll.checked  = true;
    if (roleAll)     roleAll.checked     = true;
    if (kelasInput)  kelasInput.value    = '';

    applyFilters();
}

function resetAllFilters() {
    const search = document.getElementById('searchInput');
    if (search) search.value = '';
    
    resetFilters();
}

document.addEventListener('click', function(e) {
    const form = document.getElementById('searchForm');
    const dropdown = document.getElementById('searchDropdown');
    if (form && !form.contains(e.target) && dropdown) {
        dropdown.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    initClassesList();
    initKelasAutocomplete();
});