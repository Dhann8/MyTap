// ============================================================
// filterUsers.js – Live search + cascading class filter
// ============================================================

function updateRfidStatus(userId, newStatus, selectEl) {
    if (newStatus === 'active') {
        selectEl.className = "px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-green-50 text-green-800 cursor-pointer";
    } else {
        selectEl.className = "px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-red-50 text-red-800 cursor-pointer";
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ? document.querySelector('meta[name="csrf-token"]').content
        : '';

    fetch(`/users/${userId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
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

// ── Cascading filter helpers ──────────────────────────────────

function onTingkatChange() {
    const tingkat = document.getElementById('filter-tingkat').value;
    const jurusanSel = document.getElementById('filter-jurusan');
    const nomorSel   = document.getElementById('filter-nomor');

    // Reset jurusan & nomor
    jurusanSel.value = '';
    nomorSel.innerHTML = '<option value="">Semua Nomor</option>';
    nomorSel.disabled = true;

    if (tingkat) {
        jurusanSel.disabled = false;
    } else {
        jurusanSel.disabled = true;
    }

    filterUsers();
}

function onJurusanChange() {
    const jurusan  = document.getElementById('filter-jurusan').value;
    const nomorSel = document.getElementById('filter-nomor');

    nomorSel.innerHTML = '<option value="">Semua Nomor</option>';

    if (jurusan) {
        nomorSel.disabled = false;
        const maxNomor = jurusan === 'rpl' ? 10 : 3;
        for (let i = 1; i <= maxNomor; i++) {
            const opt = document.createElement('option');
            opt.value = String(i);
            opt.textContent = i;
            nomorSel.appendChild(opt);
        }
    } else {
        nomorSel.disabled = true;
    }

    filterUsers();
}

// ── Main filter function ─────────────────────────────────────

function filterUsers() {
    const keyword  = (document.getElementById('user-search')?.value || '').toLowerCase().trim();
    const role     = document.getElementById('user-role')?.value || 'all';
    const tingkat  = document.getElementById('filter-tingkat')?.value || '';
    const jurusan  = document.getElementById('filter-jurusan')?.value || '';
    const nomor    = document.getElementById('filter-nomor')?.value || '';

    const rows      = document.querySelectorAll('.user-row');
    const resetBtn  = document.getElementById('reset-filter-btn');
    let visibleCount = 0;

    const hasFilter = keyword || role !== 'all' || tingkat || jurusan || nomor;
    if (resetBtn) {
        resetBtn.classList.toggle('hidden', !hasFilter);
    }

    rows.forEach(row => {
        const name  = row.getAttribute('data-name')  || '';
        const email = row.getAttribute('data-email') || '';
        const uid   = row.getAttribute('data-uid')   || '';
        const rowRole  = row.getAttribute('data-role')  || '';
        const rowKelas = row.getAttribute('data-kelas') || ''; // contoh: "x-rpl 1"

        // ── Match keyword
        const matchKeyword = !keyword || name.includes(keyword) || email.includes(keyword) || uid.includes(keyword);

        // ── Match role
        const matchRole = role === 'all' || rowRole === role;

        // ── Parse kelas: format "tingkat-jurusan nomor" contoh "x-rpl 1"
        // rowKelas sudah di-strtolower di blade
        let matchKelas = true;
        if (tingkat || jurusan || nomor) {
            // Pisahkan tingkat dan sisa: "x-rpl 1" → tingkatPart="x", rest="rpl 1"
            const dashIdx = rowKelas.indexOf('-');
            const tingkatPart  = dashIdx >= 0 ? rowKelas.substring(0, dashIdx) : rowKelas;       // "x"
            const afterDash    = dashIdx >= 0 ? rowKelas.substring(dashIdx + 1) : '';            // "rpl 1"
            const spaceIdx     = afterDash.indexOf(' ');
            const jurusanPart  = spaceIdx >= 0 ? afterDash.substring(0, spaceIdx) : afterDash;  // "rpl"
            const nomorPart    = spaceIdx >= 0 ? afterDash.substring(spaceIdx + 1) : '';         // "1"

            if (tingkat && tingkatPart !== tingkat.toLowerCase()) matchKelas = false;
            if (jurusan && jurusanPart !== jurusan.toLowerCase()) matchKelas = false;
            if (nomor   && nomorPart   !== nomor)                 matchKelas = false;
        }

        const visible = matchKeyword && matchRole && matchKelas;
        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });

    // Tampilkan pesan jika tidak ada hasil
    let emptyRow = document.getElementById('user-empty-row');
    if (visibleCount === 0 && hasFilter) {
        if (!emptyRow) {
            const tbody = document.querySelector('tbody');
            emptyRow = document.createElement('tr');
            emptyRow.id = 'user-empty-row';
            emptyRow.innerHTML = `<td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">Tidak ada user yang sesuai filter.</td>`;
            tbody.appendChild(emptyRow);
        }
        emptyRow.style.display = '';
    } else if (emptyRow) {
        emptyRow.style.display = 'none';
    }
}

// ── Reset all filters ─────────────────────────────────────────

function resetUserFilters() {
    const search   = document.getElementById('user-search');
    const role     = document.getElementById('user-role');
    const tingkat  = document.getElementById('filter-tingkat');
    const jurusan  = document.getElementById('filter-jurusan');
    const nomor    = document.getElementById('filter-nomor');

    if (search)  search.value  = '';
    if (role)    role.value    = 'all';
    if (tingkat) tingkat.value = '';

    if (jurusan) { jurusan.value = ''; jurusan.disabled = true; }
    if (nomor)   {
        nomor.innerHTML = '<option value="">Semua Nomor</option>';
        nomor.disabled  = true;
    }

    filterUsers();
}

// ── Init on load ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    filterUsers();
});