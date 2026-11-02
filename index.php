<?php
date_default_timezone_set('Asia/Jakarta');

$duration = 72 * 24 * 60 * 60;
$storageDir = __DIR__ . '/nexan-storage';
$timestampFile = $storageDir . '/countdown-start.txt';

if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
    http_response_code(500);
    exit('Storage directory error.');
}

if (!is_writable($storageDir)) {
    http_response_code(500);
    exit('Storage permission error.');
}

$fp = fopen($timestampFile, 'c+');
if ($fp === false || !flock($fp, LOCK_EX)) {
    if ($fp !== false) fclose($fp);
    http_response_code(500);
    exit('Countdown initialization error.');
}

$storedTimestamp = trim(stream_get_contents($fp));
if ($storedTimestamp === '' || !ctype_digit($storedTimestamp)) {
    $startTime = time();
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $startTime);
    fflush($fp);
} else {
    $startTime = (int) $storedTimestamp;
}

flock($fp, LOCK_UN);
fclose($fp);

$remaining = max(0, ($startTime + $duration) - time());
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#f8fafc" id="theme-color">
    <meta name="description" content="WWW.NEXAN.ID sedang dalam pengembangan. Nantikan pembaruan terbaru dari kami.">
    <title>WWW.NEXAN.ID</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        (() => {
            let savedTheme = null;
            try { savedTheme = localStorage.getItem('nexan-theme'); } catch (error) {}
            const systemTheme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.dataset.theme =
                ['light', 'dark'].includes(savedTheme) ? savedTheme : systemTheme;
        })();
    </script>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <div class="w-100 text-center position-relative" style="max-width: 660px;">
        <div class="position-absolute top-0 end-0">
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Ganti tema" title="Ganti tema">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>
        </div>

        <div class="mb-5">
            <a href="/" class="brand-link fw-bold tracking-widest fs-4">WWW.NEXAN<span>.ID</span></a>
        </div>

        <div class="mb-4">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-blue-700">
                <i class="fa-solid fa-circle text-green-500 text-[8px]"></i>
                Currently Under Development
            </span>
        </div>

        <h1 class="fw-bold tracking-tight mb-3 display-6 theme-text">
            Website Sedang<br class="d-none d-sm-block">Dalam Pengembangan
        </h1>

        <p class="theme-muted fs-6 leading-relaxed mx-auto" style="max-width: 480px;">
            Kami sedang melakukan pembaruan untuk menghadirkan layanan yang lebih baik.
        </p>

        <section class="mt-5">
            <p id="countdown-title" class="text-uppercase theme-muted fw-semibold small tracking-widest mb-3">
                <i class="fa-regular fa-clock me-2"></i>Estimated Launch
            </p>
            <div class="row g-2 justify-content-center">
                <div class="col-3 col-sm-2"><div class="theme-card rounded-3 py-3 px-1"><div id="days" class="time-value">00</div><div class="time-unit">Hari</div></div></div>
                <div class="col-3 col-sm-2"><div class="theme-card rounded-3 py-3 px-1"><div id="hours" class="time-value">00</div><div class="time-unit">Jam</div></div></div>
                <div class="col-3 col-sm-2"><div class="theme-card rounded-3 py-3 px-1"><div id="minutes" class="time-value">00</div><div class="time-unit">Menit</div></div></div>
                <div class="col-3 col-sm-2"><div class="theme-card rounded-3 py-3 px-1"><div id="seconds" class="time-value">00</div><div class="time-unit">Detik</div></div></div>
            </div>
        </section>

        <section class="mt-5 pt-4 border-top theme-divider">
            <p class="theme-muted small mb-2">Informasi dan update terbaru:</p>
            <a href="mailto:info@nexan.id" class="contact-link">
                <i class="fa-regular fa-envelope me-2"></i>info@nexan.id
            </a>
        </section>

        <footer class="mt-5 pt-2">
            <p class="theme-muted small mb-1">
                <strong class="theme-text">WWW.NEXAN.ID</strong>
                <span class="mx-1">·</span>Innovative Technology
            </p>
            <p class="theme-muted small mb-0">&copy; <?= date('Y') ?> All Rights Reserved.</p>
        </footer>
    </div>
</main>

<script>
    // Theme: manual preference takes precedence; otherwise follow system.
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeColor = document.getElementById('theme-color');
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    function getSavedTheme() {
        try {
            const theme = localStorage.getItem('nexan-theme');
            return ['light', 'dark'].includes(theme) ? theme : null;
        } catch (error) {
            return null;
        }
    }

    function applyTheme(theme) {
        document.documentElement.dataset.theme = theme;
        const isDark = theme === 'dark';

        themeIcon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        themeToggle.setAttribute('aria-label', isDark ? 'Aktifkan light mode' : 'Aktifkan dark mode');
        themeToggle.title = isDark ? 'Light Mode' : 'Dark Mode';
        themeColor.content = isDark ? '#111827' : '#f8fafc';
    }

    function initializeTheme() {
        applyTheme(getSavedTheme() || (systemTheme.matches ? 'dark' : 'light'));
    }

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.dataset.theme;
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        try {
            localStorage.setItem('nexan-theme', newTheme);
        } catch (error) {}

        applyTheme(newTheme);
    });

    systemTheme.addEventListener('change', event => {
        if (!getSavedTheme()) applyTheme(event.matches ? 'dark' : 'light');
    });

    initializeTheme();

    // Countdown uses the server timestamp; refresh does not reset it.
    const initialRemaining = <?= json_encode($remaining) ?>;
    const startClock = performance.now();

    const elements = {
        days: document.getElementById('days'),
        hours: document.getElementById('hours'),
        minutes: document.getElementById('minutes'),
        seconds: document.getElementById('seconds')
    };

    let countdownInterval;

    function updateCountdown() {
        const elapsed = Math.floor((performance.now() - startClock) / 1000);
        const remaining = Math.max(0, initialRemaining - elapsed);

        const days = Math.floor(remaining / 86400);
        const hours = Math.floor((remaining % 86400) / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;

        elements.days.textContent = String(days).padStart(2, '0');
        elements.hours.textContent = String(hours).padStart(2, '0');
        elements.minutes.textContent = String(minutes).padStart(2, '0');
        elements.seconds.textContent = String(seconds).padStart(2, '0');

        if (remaining <= 0) {
            clearInterval(countdownInterval);
            document.getElementById('countdown-title').textContent = 'Countdown Completed';
        }
    }

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
</script>
</body>
</html>
