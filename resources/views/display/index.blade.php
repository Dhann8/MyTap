<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ get_setting('app_name', 'Sistem Absensi RFID') }} — Display</title>
    <meta name="description" content="Layar Absensi Real-time Siswa">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #0284c7; --accent: #0ea5e9;
            --success: #16a34a; --warning: #d97706;
            --bg: #f8fafc; --bg-card: #ffffff;
            --border: #e2e8f0; --text: #0f172a; --muted: #64748b;
        }
        html, body { height: 100%; width: 100%; overflow: hidden; background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; }
        body::before {
            content: ''; position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 70% 40% at 15% 0%, rgba(14,165,233,.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 85% 100%, rgba(2,132,199,.05) 0%, transparent 60%);
            pointer-events: none; z-index: 0;
        }
        body::after {
            content: ''; position: fixed; inset: 0;
            background-image: linear-gradient(rgba(14,165,233,.06) 1px, transparent 1px), linear-gradient(90deg, rgba(14,165,233,.06) 1px, transparent 1px);
            background-size: 48px 48px; pointer-events: none; z-index: 0;
        }
        .page-wrap {
            position: relative; z-index: 1;
            display: grid; grid-template-rows: auto 1fr auto;
            height: 100vh; padding: 24px 36px; gap: 20px;
        }
        /* Header */
        header { display: flex; align-items: center; justify-content: space-between; }
        .school-logo { display: flex; align-items: center; gap: 14px; }
        .school-logo .icon {
            width: 52px; height: 52px; border-radius: 14px; font-size: 26px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(14,165,233,.5);
        }
        .school-name { font-size: clamp(1rem,2vw,1.35rem); font-weight: 700; line-height: 1.2; color: var(--text); }
        .school-sub  { font-size: .78rem; color: var(--muted); margin-top: 2px; }
        .header-badge {
            display: flex; align-items: center; gap: 8px; padding: 8px 18px;
            border-radius: 999px; border: 1px solid var(--border); background: var(--bg-card);
            box-shadow: 0 1px 4px rgba(0,0,0,.06); font-size: .82rem; color: var(--success);
        }
        .dot-live { width: 8px; height: 8px; border-radius: 50%; background: var(--success); animation: pulse-dot 1.4s ease-in-out infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }
        /* Hero */
        .hero { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .page-title { font-size: clamp(.85rem,1.5vw,1.05rem); font-weight: 600; letter-spacing: .25em; text-transform: uppercase; color: var(--primary); margin-bottom: 10px; }
        .clock-display {
            font-family: 'Orbitron', monospace; font-size: clamp(5rem,14vw,10rem); font-weight: 900; line-height: 1; letter-spacing: .05em;
            background: linear-gradient(135deg, #0369a1 0%, var(--primary) 50%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: clock-glow 3s ease-in-out infinite alternate;
        }
        @keyframes clock-glow { from{filter:drop-shadow(0 2px 8px rgba(2,132,199,.2))} to{filter:drop-shadow(0 4px 18px rgba(14,165,233,.35))} }
        .date-display { font-size: clamp(.9rem,1.8vw,1.2rem); color: var(--muted); margin-top: 6px; letter-spacing: .06em; }
        .hero-divider { width: min(380px,60vw); height: 2px; margin: 18px auto; background: linear-gradient(90deg, transparent, var(--primary), transparent); opacity: .4; }
        /* Notif */
        .notif-area { min-height: 160px; display: flex; align-items: center; justify-content: center; }
        .notif-idle { display: flex; flex-direction: column; align-items: center; gap: 10px; color: var(--muted); }
        .notif-idle .idle-icon { font-size: 2.4rem; opacity: .5; }
        .notif-card {
            position: relative; border-radius: 20px; padding: 28px 48px; text-align: center;
            max-width: 700px; width: 100%;
            animation: notif-enter .5s cubic-bezier(.22,1,.36,1) both;
        }
        .notif-card.s-hadir     { border: 1.5px solid rgba(22,163,74,.25);  background: rgba(240,253,244,1);   box-shadow: 0 4px 30px rgba(22,163,74,.1); }
        .notif-card.s-terlambat { border: 1.5px solid rgba(217,119,6,.25);  background: rgba(255,251,235,1);   box-shadow: 0 4px 30px rgba(217,119,6,.1); }
        .notif-card.s-pulang    { border: 1.5px solid rgba(2,132,199,.25);  background: rgba(240,249,255,1);   box-shadow: 0 4px 30px rgba(2,132,199,.1); }
        @keyframes notif-enter { from{opacity:0;transform:translateY(24px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
        .notif-chip { display:inline-flex; align-items:center; gap:6px; padding:5px 16px; border-radius:999px; font-size:.75rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; margin-bottom:14px; }
        .notif-chip.hadir     { background:rgba(22,163,74,.12);  color:#15803d; border:1px solid rgba(22,163,74,.25); }
        .notif-chip.terlambat { background:rgba(217,119,6,.12);  color:#b45309; border:1px solid rgba(217,119,6,.25); }
        .notif-chip.pulang    { background:rgba(2,132,199,.12);  color:#0369a1; border:1px solid rgba(2,132,199,.25); }
        .notif-name { font-size:clamp(1.8rem,4vw,2.8rem); font-weight:900; line-height:1.1; color: var(--text); }
        .notif-class { font-size:clamp(.9rem,1.6vw,1.1rem); color:var(--muted); margin-top:4px; font-weight:500; }
        .notif-time { font-family:'Orbitron',monospace; font-size:clamp(1.4rem,3vw,2rem); font-weight:700; margin-top:10px; letter-spacing:.08em; }
        .notif-time.hadir     { color:#16a34a; }
        .notif-time.terlambat { color:#d97706; }
        .notif-time.pulang    { color:#0284c7; }
        .notif-message { margin-top:12px; font-size:clamp(.85rem,1.4vw,1rem); color:#64748b; font-style:italic; }
        /* Stats */
        .stats-bar { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
        .stat-card { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:16px 22px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 6px rgba(0,0,0,.06); }
        .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
        .stat-icon.hadir     { background:rgba(22,163,74,.1);  }
        .stat-icon.terlambat { background:rgba(217,119,6,.1); }
        .stat-icon.pulang    { background:rgba(2,132,199,.1); }
        .stat-icon.total     { background:rgba(2,132,199,.08); }
        .stat-info { flex:1; min-width:0; }
        .stat-label { font-size:.72rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); font-weight:600; }
        .stat-value { font-family:'Orbitron',monospace; font-size:1.7rem; font-weight:700; line-height:1.1; margin-top:2px; }
        .stat-value.hadir     { color:#16a34a; }
        .stat-value.terlambat { color:#d97706; }
        .stat-value.pulang    { color:#0284c7; }
        .stat-value.total     { color:#0369a1; }
        /* Log sidebar */
        .recent-log { position:fixed; right:36px; top:50%; transform:translateY(-50%); width:230px; display:flex; flex-direction:column; gap:8px; z-index:10; }
        .log-title { font-size:.7rem; text-transform:uppercase; letter-spacing:.15em; color:var(--muted); font-weight:600; margin-bottom:4px; }
        .log-item { background:var(--bg-card); border:1px solid var(--border); border-radius:10px; padding:10px 14px; box-shadow:0 1px 4px rgba(0,0,0,.05); animation:log-slide .4s ease both; }
        @keyframes log-slide { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
        .log-name { font-size:.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--text); }
        .log-meta { font-size:.7rem; color:var(--muted); margin-top:2px; display:flex; justify-content:space-between; align-items:center; }
        .log-status { font-size:.65rem; font-weight:600; padding:1px 7px; border-radius:999px; }
        .log-status.hadir     { background:rgba(22,163,74,.1);  color:#15803d; border:1px solid rgba(22,163,74,.2); }
        .log-status.terlambat { background:rgba(217,119,6,.1);  color:#b45309; border:1px solid rgba(217,119,6,.2); }
        .log-status.pulang    { background:rgba(2,132,199,.1);  color:#0369a1; border:1px solid rgba(2,132,199,.2); }
    </style>
</head>
<body>

<div class="page-wrap">
    {{-- HEADER --}}
    <header>
        <div class="school-logo">
            <div class="icon"></div>
            <div>
                <div class="school-name">{{ get_setting('app_name', 'Sistem Absensi RFID') }}</div>
                <div class="school-sub">Sistem Absensi Digital Berbasis RFID</div>
            </div>
        </div>
        <div class="header-badge">
            <div class="dot-live"></div>
            <span>LIVE</span>
        </div>
    </header>

    <div class="hero">
        <div class="page-title">✨ Selamat Datang — Tap Kartu RFID Anda</div>
        <div class="clock-display" id="js-clock">--:--:--</div>
        <div class="date-display" id="js-date">---</div>
        <div class="hero-divider"></div>

        <div class="notif-area" id="js-notif-area">
            @if($latest)
                @php
                    $isSuccess = is_array($latest) ? ($latest['success'] ?? true) : true;
                    $st = strtolower(is_array($latest) ? ($latest['status'] ?? 'hadir') : ($latest->status ?? 'hadir'));
                    $cls = $isSuccess ? (in_array($st, ['hadir','terlambat','pulang']) ? $st : 'hadir') : 'terlambat';
                    $label = $isSuccess ? ($st === 'hadir' ? '✅ Absen Masuk' : ($st === 'terlambat' ? '⚠️ Terlambat' : '🏁 Pulang')) : '⚠️ ' . (is_array($latest) ? ($latest['status'] ?? 'Pemberitahuan') : 'Pemberitahuan');
                    $name = is_array($latest) ? ($latest['name'] ?? 'Kartu RFID') : ($latest->user->name ?? 'Kartu RFID');
                    $kelas = is_array($latest) ? ($latest['kelas'] ?? '-') : ($latest->user->kelas ?? '-');
                    $timeIn = is_array($latest) ? ($latest['time_in'] ?? '') : ($latest->time_in ?? '');
                    $msg = is_array($latest) && !empty($latest['message']) ? $latest['message'] : 'Absensi tercatat';
                @endphp
                <div class="notif-card s-{{ $cls }}">
                    <div class="notif-chip {{ $cls }}">{{ $label }}</div>
                    <div class="notif-name">{{ $name }}</div>
                    @if($kelas !== '-')
                    <div class="notif-class">Kelas {{ $kelas }}</div>
                    @endif
                    <div class="notif-time {{ $cls }}">{{ $timeIn }}</div>
                    <div class="notif-message">"{{ $msg }}"</div>
                </div>
            @else
                <div class="notif-idle">
                    <div class="idle-icon">📡</div>
                    <p>Menunggu scan kartu RFID...</p>
                </div>
            @endif
        </div>
    </div>

    {{-- STATS BAR --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon hadir"></div>
            <div class="stat-info">
                <div class="stat-label">Hadir</div>
                <div class="stat-value hadir" id="stat-hadir">{{ $stats['hadir'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon terlambat"></div>
            <div class="stat-info">
                <div class="stat-label">Terlambat</div>
                <div class="stat-value terlambat" id="stat-terlambat">{{ $stats['terlambat'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pulang"></div>
            <div class="stat-info">
                <div class="stat-label">Pulang</div>
                <div class="stat-value pulang" id="stat-pulang">{{ $stats['pulang'] }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total"></div>
            <div class="stat-info">
                <div class="stat-label">Total Hari Ini</div>
                <div class="stat-value total" id="stat-total">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- RECENT LOG SIDEBAR --}}
<div class="recent-log" id="js-log">
    <div class="log-title"> Absen Terbaru</div>
    @foreach($recentLog as $item)
        @php $lc = in_array(strtolower($item->status),['hadir','terlambat','pulang']) ? strtolower($item->status) : 'hadir'; @endphp
        <div class="log-item">
            <div class="log-name">{{ $item->user->name }}</div>
            <div class="log-meta">
                <span>Kelas {{ $item->user->kelas }}</span>
                <span class="log-status {{ $lc }}">{{ $item->status }}</span>
            </div>
            <div class="log-meta"><span>{{ $item->time_in }}</span></div>
        </div>
    @endforeach
</div>

<script>
const HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
function pad(n){ return String(n).padStart(2,'0'); }
function updateClock(){
    const n = new Date();
    document.getElementById('js-clock').textContent = `${pad(n.getHours())}:${pad(n.getMinutes())}:${pad(n.getSeconds())}`;
    document.getElementById('js-date').textContent  = `${HARI[n.getDay()]}, ${n.getDate()} ${BULAN[n.getMonth()]} ${n.getFullYear()}`;
}
updateClock(); setInterval(updateClock, 1000);

let lastId = {{ $latest ? (is_array($latest) ? ($latest['id'] ?? 'null') : $latest->id) : 'null' }};

const MESSAGES = {
    hadir:     ['Selamat datang! Semangat belajar hari ini','Kehadiran Anda tercatat. Tetap semangat!','Hadir tepat waktu, awal yang baik!'],
    terlambat: ['Lebih awal besok ya! Semangat!','Jangan menyerah, selesaikan hari ini dengan baik','Tetap semangat, masih ada waktu belajar!'],
    pulang:    ['Selamat pulang! Jaga diri di jalan','Sampai jumpa besok! Istirahat yang cukup','Pulang dengan selamat ya!'],
};

function getCls(status, success){
    if (success === false) return 'terlambat';
    const s = (status || '').toLowerCase();
    return ['hadir','terlambat','pulang'].includes(s) ? s : 'hadir';
}

function getLabel(status, success){
    if (success === false) return '⚠️ ' + (status || 'Pemberitahuan');
    const s = (status || '').toLowerCase();
    if (s === 'hadir') return '✅ Absen Masuk';
    if (s === 'pulang') return '🏁 Absen Pulang';
    if (s === 'terlambat') return '⚠️ Terlambat';
    return 'ℹ️ ' + status;
}

function randMsg(cls){ const a=MESSAGES[cls]||MESSAGES.hadir; return a[Math.floor(Math.random()*a.length)]; }

function renderNotif(att){
    const cls = getCls(att.status, att.success);
    const label = getLabel(att.status, att.success);
    const msg = att.message ? att.message : randMsg(cls);
    const kelasText = (att.kelas && att.kelas !== '-') ? `<div class="notif-class">Kelas ${att.kelas}</div>` : '';

    document.getElementById('js-notif-area').innerHTML = `
        <div class="notif-card s-${cls}">
            <div class="notif-chip ${cls}">${label}</div>
            <div class="notif-name">${att.name}</div>
            ${kelasText}
            <div class="notif-time ${cls}">${att.time_in}</div>
            <div class="notif-message">"${msg}"</div>
        </div>`;
}

function renderLog(items){
    let h = '<div class="log-title"> Absen Terbaru</div>';
    items.forEach(i => {
        const c = getCls(i.status, true);
        h += `<div class="log-item">
            <div class="log-name">${i.name}</div>
            <div class="log-meta"><span>Kelas ${i.kelas}</span><span class="log-status ${c}">${i.status}</span></div>
            <div class="log-meta"><span>${i.time_in}</span></div>
        </div>`;
    });
    document.getElementById('js-log').innerHTML = h;
}

async function poll(){
    try {
        const r = await fetch(`/api/display/latest?last_id=${lastId ?? ''}`, {headers:{'Accept':'application/json'}});
        if(!r.ok) return;
        const d = await r.json();
        if(d.stats){
            document.getElementById('stat-hadir').textContent     = d.stats.hadir;
            document.getElementById('stat-terlambat').textContent = d.stats.terlambat;
            document.getElementById('stat-pulang').textContent    = d.stats.pulang;
            document.getElementById('stat-total').textContent     = d.stats.total;
        }
        if(d.latest && d.latest.id != lastId){
            lastId = d.latest.id;
            renderNotif(d.latest);
        }
        if(d.recentLog) renderLog(d.recentLog);
    } catch(e){}
}
setInterval(poll, 5000);
</script>
</body>
</html>