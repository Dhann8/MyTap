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
                            <i class="fa-solid fa-calendar-days absolute left-3 text-gray-400"></i>
                            <input type="date" id="date-filter" onchange="filterAttendance()" onclick="this.showPicker()"
                                onfocus="this.showPicker()"
                                class="[&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-inner-spin-button]:hidden pl-9 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm appearance-none">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="exportToExcel()"
                            class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                            <i class="fa-solid fa-file-excel"></i> Export
                        </button>
                        <button
                            class="px-4 py-2 text-sm bg-gray-50 hover:bg-gray-100 border border-gray-300 text-gray-700 font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-filter"></i> Filter
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
                                    Email</th>
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
                                                <tr class="hover:bg-gray-50 transition-colors duration-200 attendance-row">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $attendances->firstItem() + $index }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-medium text-gray-900">{{ $attendance->user->name ?? 'User Terhapus' }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">{{ $attendance->user->uid ?? '-' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                        {{ $attendance->user->email ?? '-' }}
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
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('attendance.show', $attendance->id) }}"
                                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada data absensi ditemukan.
                                    </td>
                                    endtr>
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
    <script>
        window.autocompleteUrl = "{{ route('attendance.autocomplete') }}";
    </script>

    <script src="{{ asset('js/filterAttendance.js') }}"></script>
@endsection