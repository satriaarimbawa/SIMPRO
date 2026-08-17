<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | SIMPRO</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(2deg); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.08); }
        }
        .animate-float {
            animation: floatSlow 5s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-blue-500 selection:text-white">

    <!-- Background Decorative Glow Blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/30 rounded-full blur-[120px] pointer-events-none animate-pulse-glow"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none animate-pulse-glow" style="animation-delay: 2s;"></div>

    <div class="relative z-10 max-w-xl w-full px-6 py-12 text-center" x-data="{ 
        mouseX: 0, 
        mouseY: 0, 
        searchQuery: '',
        handleMouseMove(e) {
            const rect = $el.getBoundingClientRect();
            this.mouseX = (e.clientX - rect.left - rect.width / 2) / 25;
            this.mouseY = (e.clientY - rect.top - rect.height / 2) / 25;
        },
        resetTilt() {
            this.mouseX = 0;
            this.mouseY = 0;
        },
        performSearch() {
            if (this.searchQuery.trim()) {
                window.location.href = '/arsip?no_spk=' + encodeURIComponent(this.searchQuery.trim());
            }
        }
    }" @mousemove="handleMouseMove" @mouseleave="resetTilt">

        <!-- Interactive Parallax Card Container -->
        <div class="glass-card rounded-3xl p-8 sm:p-10 shadow-2xl text-slate-800 transition-transform duration-200 ease-out"
             :style="`transform: perspective(1000px) rotateX(${-mouseY}deg) rotateY(${mouseX}deg);`">
            
            <!-- Floating Graphic Badge -->
            <div class="relative w-28 h-28 mx-auto mb-6 flex items-center justify-center animate-float">
                <div class="absolute inset-0 bg-blue-500/20 rounded-3xl blur-xl animate-pulse"></div>
                <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-3xl shadow-lg flex items-center justify-center transform rotate-6 border border-white/30 text-white">
                    <span class="material-symbols-outlined text-5xl">manage_search</span>
                </div>
            </div>

            <!-- Error Code & Title -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold font-mono tracking-widest uppercase mb-4 border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                Error 404
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight font-inter mb-3">
                Halaman Tidak Ditemukan
            </h1>

            <p class="text-sm text-slate-600 font-inter mb-8 leading-relaxed">
                Tautan SPK yang Anda tuju mungkin salah, telah diperbarui, atau ID dalam URL tidak valid.
            </p>

            <!-- Quick Search Bar -->
            <form @submit.prevent="performSearch" class="relative max-w-md mx-auto mb-8">
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-4 text-slate-400">search</span>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari Nomor SPK atau Nama Dinas..." 
                           class="w-full pl-11 pr-24 py-3 text-sm rounded-xl border border-slate-200 bg-white/90 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent shadow-inner font-inter text-slate-800 placeholder-slate-400">
                    <button type="submit" 
                            class="absolute right-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-all flex items-center gap-1 shadow-sm">
                        <span>Cari</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                </div>
            </form>

            <!-- Action Navigation Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="/dashboard" 
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium transition-all shadow-md flex items-center justify-center gap-2 group">
                    <span class="material-symbols-outlined text-lg">dashboard</span>
                    <span>Ke Dashboard</span>
                </a>
                <a href="/arsip" 
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">inventory_2</span>
                    <span>Buka Arsip SPK</span>
                </a>
                <button type="button" 
                        onclick="window.history.back()" 
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-medium transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    <span>Kembali</span>
                </button>
            </div>
        </div>

        <!-- Footer Note -->
        <p class="text-xs text-slate-500 font-jetbrains mt-6">
            SIMPRO &bull; Sistem Informasi & Pengelolaan SPK
        </p>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
