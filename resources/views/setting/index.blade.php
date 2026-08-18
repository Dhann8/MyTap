@extends('layout.app')

@section('content')
    <div class="flex min-h-screen bg-gray-50/50">
        <x-sidebar />
        <main class="flex-1 ml-64">
            <x-Header />

            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola konfigurasi absensi, WhatsApp Gateway, dan preferensi aplikasi di bawah ini.</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Baris Atas: Jam, Tema, WiFi (3 Kolom) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Pengaturan Jam Absensi -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jam Absensi
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="minimal_datang" class="block text-xs font-semibold text-gray-700 mb-1.5">Minimal Jam Datang (Tap In)</label>
                                    <input type="time" id="minimal_datang" name="minimal_datang" value="{{ $settings['minimal_datang'] }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                                    <p class="text-[10px] text-gray-400 mt-1">*Siswa tidak bisa tap sebelum jam ini.</p>
                                </div>
                                
                                <div>
                                    <label for="jam_masuk" class="block text-xs font-semibold text-gray-700 mb-1.5">Batas Jam Masuk (Tepat Waktu)</label>
                                    <input type="time" id="jam_masuk" name="jam_masuk" value="{{ $settings['jam_masuk'] }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                                </div>

                                <div>
                                    <label for="toleransi_terlambat" class="block text-xs font-semibold text-gray-700 mb-1.5">Toleransi Keterlambatan (Menit)</label>
                                    <input type="number" id="toleransi_terlambat" name="toleransi_terlambat" value="{{ $settings['toleransi_terlambat'] }}" min="0" max="60" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                                </div>

                                <div>
                                    <label for="jam_pulang" class="block text-xs font-semibold text-gray-700 mb-1.5">Jam Pulang (Tap Out)</label>
                                    <input type="time" id="jam_pulang" name="jam_pulang" value="{{ $settings['jam_pulang'] }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Tema & Tampilan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                    Tema & Tampilan
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="app_name" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Instansi / Sekolah</label>
                                    <input type="text" id="app_name" name="app_name" value="{{ $settings['app_name'] }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all">
                                </div>

                                <div>
                                    <label for="theme_mode" class="block text-xs font-semibold text-gray-700 mb-1.5">Mode Tema Tampilan</label>
                                    <select id="theme_mode" name="theme_mode" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all bg-white cursor-pointer appearance-none">
                                        <option value="light" {{ $settings['theme_mode'] == 'light' ? 'selected' : '' }}>Terang (Light Mode)</option>
                                        <option value="dark" {{ $settings['theme_mode'] == 'dark' ? 'selected' : '' }}>Gelap (Dark Mode)</option>
                                        <option value="system" {{ $settings['theme_mode'] == 'system' ? 'selected' : '' }}>Ikuti Sistem (Auto)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="language" class="block text-xs font-semibold text-gray-700 mb-1.5">Bahasa Aplikasi</label>
                                    <select id="language" name="language" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all bg-white cursor-pointer appearance-none">
                                        <option value="id" {{ $settings['language'] == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                        <option value="en" {{ $settings['language'] == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Konfigurasi Jaringan WiFi (ESP32) -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                    Jaringan Perangkat (ESP32)
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="wifi_ssid" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama WiFi (SSID)</label>
                                    <input type="text" id="wifi_ssid" name="wifi_ssid" value="{{ $settings['wifi_ssid'] ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all">
                                    <p class="text-[10px] text-gray-400 mt-1">*Nama WiFi untuk koneksi alat scan RFID.</p>
                                </div>

                                <div>
                                    <label for="wifi_password" class="block text-xs font-semibold text-gray-700 mb-1.5">Password WiFi</label>
                                    <div class="relative">
                                        <input type="password" id="wifi_password" name="wifi_password" value="{{ $settings['wifi_password'] ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all pr-10">
                                        <button type="button" onclick="toggleWifiPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg id="icon-eye-wifi" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label for="server_ip" class="block text-xs font-semibold text-gray-700 mb-1.5">IP Address Server</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                                        </div>
                                        <input type="text" id="server_ip" name="server_ip"
                                            value="{{ $settings['server_ip'] ?? '' }}"
                                            placeholder="Contoh: 192.168.1.100:8000"
                                            pattern="^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?):\d{1,5}$"
                                            title="Masukkan IP beserta port, misal: 192.168.1.100:8000"
                                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all font-mono tracking-wide">
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">*IP Address server Laravel yang bisa diakses oleh perangkat ESP32.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Baris Bawah: WA Gateway -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                WhatsApp Gateway
                            </h3>
                            <span class="flex items-center gap-1.5 text-xs font-medium px-2.5 py-0.5 rounded-full text-green-600 bg-green-50 border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-600 animate-pulse"></span>
                                Online
                            </span>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex gap-3">
                                <button type="button" onclick="alert('Fitur Scan QR belum tersedia di halaman ini.')" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    Scan QR / Re-Auth
                                </button>
                                <button type="button" onclick="alert('Diskoneksi WA belum tersedia di halaman ini.')" class="px-4 py-2 bg-white border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-gray-700 text-xs font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout WA
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="wa_gateway_url" class="block text-xs font-semibold text-gray-700 mb-1.5">URL Server WA Gateway</label>
                                    <input type="url" id="wa_gateway_url" name="wa_gateway_url" value="{{ $settings['wa_gateway_url'] }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                                </div>

                                <div>
                                    <label for="wa_api_key" class="block text-xs font-semibold text-gray-700 mb-1.5">API Key / Secret Token</label>
                                    <input type="password" id="wa_api_key" name="wa_api_key" value="{{ $settings['wa_api_key'] }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" id="auto_send_wa" name="auto_send_wa" value="1" {{ $settings['auto_send_wa'] == '1' ? 'checked' : '' }} class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                                    </div>
                                    <label for="auto_send_wa" class="text-sm font-medium text-gray-700 cursor-pointer">
                                        Kirim Notifikasi WA Otomatis Saat Tap Absensi
                                        <p class="text-xs text-gray-400 font-normal mt-0.5">Sistem akan mengirim pesan ke nomor WA siswa/orang tua setiap kali melakukan tap kartu.</p>
                                    </label>
                                </div>
                                
                                <div>
                                    <label for="delay_wa" class="block text-xs font-semibold text-gray-700 mb-1.5">Delay Pengiriman (Detik)</label>
                                    <input type="number" id="delay_wa" name="delay_wa" value="{{ $settings['delay_wa'] }}" min="1" max="60" required class="w-32 px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all">
                                    <p class="text-[10px] text-gray-400 mt-1">*Mencegah nomor WA terblokir karena dianggap spam.</p>
                                </div>
                            </div>

                            <div>
                                <label for="template_wa_hadir" class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center justify-between">
                                    <span>Template Pesan Hadir</span>
                                    <span class="text-[10px] text-gray-400 font-normal">Variabel: {nama}, {waktu}</span>
                                </label>
                                <textarea id="template_wa_hadir" name="template_wa_hadir" rows="4" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all resize-none">{{ $settings['template_wa_hadir'] }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200/80">
                        <button type="reset" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all flex items-center gap-2 shadow-sm shadow-blue-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
<script>
    function toggleWifiPassword() {
        const input = document.getElementById('wifi_password');
        const icon  = document.getElementById('icon-eye-wifi');
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';

        icon.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
    }
</script>
@endpush