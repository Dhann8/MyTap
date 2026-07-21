@extends('layout.app')
@section('content')

    <div class="flex min-h-screen">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                <div class="max-w-2xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 font-sans">Tambah User Baru</h2>
                            <p class="text-xs text-gray-400 mt-1">Daftarkan akun administrator baru atau siswa baru beserta kartu RFID-nya.</p>
                        </div>
                        <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm bg-gray-50 hover:bg-gray-100 border border-gray-300 text-gray-700 font-medium rounded-lg transition-all duration-200 flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-6">
                            @if ($errors->any())
                                <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                                    <ul class="list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukan Nama Lengkap" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@gmail.com" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Password</label>
                                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">UID RFID</label>
                                        <input type="text" name="uid" value="{{ old('uid') }}" required placeholder="010320135298" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Role</label>
                                        <select id="role" name="role" onchange="toggleKelasInput()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status RFID</label>
                                        <select name="rfid_status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="active" {{ old('rfid_status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="inactive" {{ old('rfid_status') === 'inactive' ? 'selected' : '' }}>Nonaktif (Blokir Absen)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div id="kelas_group" class="relative">
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kelas</label>
                                        <input type="text" id="kelas" name="kelas" autocomplete="off" value="{{ old('kelas') }}" placeholder="X-rpl 1, X-dkv 1..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <div id="kelas_suggestions" class="hidden absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto divide-y divide-gray-100"></div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">No. HP / WhatsApp</label>
                                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08123456789" maxlength="15" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100">Batal</a>
                                    <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/user-form.js') }}"></script>
@endsection
