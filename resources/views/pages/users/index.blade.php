@extends('layout.app')
@section('content')

    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-50 rounded-xl border border-green-200 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-between mb-6 text-gray-600">
                    <div class="flex items-center gap-3 flex-wrap flex-1">
                        {{-- Search Form --}}
                        <form id="searchForm" onsubmit="return false;" class="relative">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari nama, email atau UID..."
                                    value="{{ $search }}"
                                    class="pl-9 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm w-64"
                                    autocomplete="off" oninput="liveSearchUsers(this.value)">
                            </div>
                            <div id="searchDropdown"
                                class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            </div>
                        </form>

                        {{-- Single Filter Button with Popover --}}
                        <div class="relative inline-block text-left" id="filter-dropdown-container">
                            <button onclick="toggleFilterDropdown()" type="button" class="px-4 py-2 text-sm bg-gray-50 hover:bg-gray-100 border border-gray-300 text-gray-700 font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                            
                            {{-- Dropdown Card --}}
                            <div id="filter-dropdown-card" class="hidden absolute left-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4 space-y-4">
                                {{-- Radio Angkatan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Angkatan</label>
                                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-angkatan" value="all" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">Semua</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-angkatan" value="X" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">X</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-angkatan" value="XI" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">XI</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-angkatan" value="XII" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">XII</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Radio Jurusan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jurusan</label>
                                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-jurusan" value="all" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">Semua</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-jurusan" value="RPL" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">RPL</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-jurusan" value="DKV" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">DKV</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Search Kelas Autocomplete --}}
                                <div class="relative">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kelas</label>
                                    <input type="text" id="filter-kelas-search" autocomplete="off" placeholder="Ketik kelas (X-rpl 1, XI-dkv 2)..." 
                                        class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div id="filter-kelas-suggestions" class="hidden absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto divide-y divide-gray-100"></div>
                                </div>

                                {{-- Radio Role --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Role</label>
                                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-role" value="all" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">Semua</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-role" value="admin" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">Admin</span>
                                        </label>
                                        <label class="inline-flex items-center text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="filter-role" value="user" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 font-medium text-xs">User</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-2 border-t border-gray-100">
                                    <button onclick="resetFilters()" type="button" class="flex-1 px-3 py-1.5 text-xs text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors font-medium">Reset</button>
                                    <button onclick="applyFilters()" type="button" class="flex-1 px-3 py-1.5 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors font-medium">Terapkan</button>
                                </div>
                            </div>
                        </div>

                        {{-- Reset Filter Button --}}
                        <button onclick="resetAllFilters()" id="reset-filter-btn" class="hidden px-3 py-2 text-sm text-gray-500 hover:text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fa-solid fa-xmark mr-1"></i> Reset All
                        </button>
                    </div>

                    <a href="{{ route('users.create') }}"
                        class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-user-plus"></i> Tambah User
                    </a>
                </div>

                <div class="border border-gray-200 rounded-xl shadow-sm overflow-hidden bg-white">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama / Email</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">UID RFID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status RFID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $index => $u)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 user-row"
                                    data-name="{{ strtolower($u->name) }}"
                                    data-email="{{ strtolower($u->email) }}"
                                    data-uid="{{ strtolower($u->uid) }}"
                                    data-role="{{ $u->role }}"
                                    data-kelas="{{ strtolower($u->kelas ?? '') }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $users->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $u->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                        {{ $u->uid }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $u->kelas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <select onchange="updateRfidStatus({{ $u->id }}, this.value, this)" 
                                            class="px-2.5 py-1 text-xs font-semibold rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-150 cursor-pointer {{ ($u->rfid_status ?? 'active') === 'active' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                                            <option value="active" {{ ($u->rfid_status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="inactive" {{ ($u->rfid_status ?? 'active') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                        <a href="{{ route('users.edit', $u->id) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            Edit
                                        </a>
                                        @if (auth()->id() !== $u->id)
                                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Semua data absen terkait juga akan hilang.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada data user ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4" id="pagination-container">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>

    <script>
        window.autocompleteUrl = "{{ route('users.autocomplete') }}";
        window.csrfToken = "{{ csrf_token() }}";
        window.authUserId = "{{ auth()->id() }}";
        
        function toggleFilterDropdown() {
            const card = document.getElementById('filter-dropdown-card');
            card.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('filter-dropdown-container');
            const card = document.getElementById('filter-dropdown-card');
            if (container && !container.contains(e.target) && card) {
                card.classList.add('hidden');
            }
        });
    </script>
    <script src="{{ asset('js/filterUsers.js') }}"></script>
@endsection
