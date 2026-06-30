@extends('layout.app')
@section('content')

    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                <div class="flex items-center justify-between mb-6 text-gray-600">
                    <div class="flex items-center gap-4">
                        <form id="searchForm" action="{{ url()->current() }}" method="GET" class="relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    placeholder="Cari nama atau UID..."
                                    class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none"
                                    autocomplete="off" oninput="liveSearch(this.value)">
                            </div>

                            <div id="searchDropdown"
                                class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            </div>
                        </form>

                        <div class="relative flex items-center max-w-xs">
                            <select id="status-filter" onchange="filterAttendance()"
                                class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none pr-8">
                                <option value="all">Semua Status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alpa">Alpa</option>
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 absolute right-2.5 text-gray-400 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>

                        <div class="relative flex items-center max-w-xs">
                            <select id="kelas-filter" onchange="filterAttendance()"
                                class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none pr-8">
                                <option value="all">Semua Kelas</option>
                                @foreach ($availableClasses as $cls)
                                    <option value="{{ $cls }}">{{ $cls }}</option>
                                @endforeach
                            </select>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 absolute right-2.5 text-gray-400 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>

                        <div class="relative flex items-center max-w-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 absolute left-3 text-gray-400 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            <input type="date" id="date-filter" onchange="filterAttendance()" onclick="this.showPicker()"
                                onfocus="this.showPicker()"
                                class="[&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-inner-spin-button]:hidden [&::-webkit-clear-button]:hidden pl-9 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="openExportModal()"
                            class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                            <i class="fa-solid fa-file-excel"></i> Export
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl shadow-sm overflow-hidden bg-white">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nama / UID RFID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jam
                                    Masuk</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="attendance-table-body">
                            @forelse ($attendances as $index => $attendance)
                                                <tr class="hover:bg-gray-50 transition-colors duration-200 attendance-row" data-kelas="{{ $attendance->user->kelas ?? '' }}">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $attendances->firstItem() + $index }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-medium text-gray-900">{{ $attendance->user->name ?? 'User Terhapus' }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">{{ $attendance->user->uid ?? '-' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        {{ $attendance->user->kelas ?? '-' }}
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 date-cell"
                                                        data-raw-date="{{ $attendance->date }}">
                                                        {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ substr($attendance->time_in, 0, 5) }} WIB
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap status-cell" data-status="{{ $attendance->status }}">
                                                        <span
                                                            class="px-2 py-1 text-xs font-medium rounded-full {{ 
                                                                                                                                                                        $attendance->status === 'Hadir' ? 'bg-green-100 text-green-800' :
                                ($attendance->status === 'Sakit' ? 'bg-yellow-100 text-yellow-800' :
                                    ($attendance->status === 'Izin' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                                            {{ $attendance->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                                        <a href="{{ route('attendance.show', $attendance->id) }}"
                                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                            Detail
                                                        </a>
                                                        <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada data absensi ditemukan.
                                    </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            </div>
        </main>
    </div>

    {{-- Modal Export Excel --}}
    <div id="export-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeExportModal()"></div>

        {{-- Modal Card --}}
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 animate-fadeIn">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Export ke Excel</h3>
                </div>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-5">Pilih periode data yang ingin diekspor.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Bulan</label>
                    <div class="relative">
                        <select id="export-month"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none pr-8">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tahun</label>
                    <div class="relative">
                        <select id="export-year"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none pr-8">
                            <option value="">Semua Tahun</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="closeExportModal()"
                    class="flex-1 px-4 py-2.5 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 font-medium transition-colors">
                    Batal
                </button>
                <button onclick="exportToExcel(); closeExportModal();"
                    class="flex-1 px-4 py-2.5 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        .animate-fadeIn { animation: fadeIn 0.15s ease-out; }
    </style>

    <script>
        window.autocompleteUrl = "{{ route('attendance.autocomplete') }}";

        function openExportModal() {
            const modal = document.getElementById('export-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExportModal() {
            const modal = document.getElementById('export-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

    <script src="{{ asset('js/filterAttendance.js') }}"></script>
@endsection