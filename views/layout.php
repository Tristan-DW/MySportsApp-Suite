<?php
$user = current_user();
$currentRoute = $_GET['route'] ?? 'dashboard';

$roleLabel = 'Admin';
if ($user) {
    switch ($user['role']) {
        case 'super_admin':
            $roleLabel = 'Super Admin';
            break;
        case 'support':
            $roleLabel = 'Support';
            break;
        case 'accounting':
            $roleLabel = 'Accounting';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars(app_name()) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#020617',
                            surface: '#020c1a',
                            blue: '#0ea5e9',
                            orange: '#fb923c',
                            accent: '#1e293b',
                        },
                    },
                    boxShadow: {
                        'glass': '0 18px 45px rgba(15,23,42,0.85)',
                    },
                }
            }
        };
    </script>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
                         Roboto, Helvetica, Arial, sans-serif;
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #020617;
        }
        ::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-brand-bg via-slate-950 to-black text-slate-100">
<div class="flex h-screen">

    <?php include BASE_PATH . '/views/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="relative z-10">
            <div class="px-6 py-4 md:px-10 md:py-5 border-b border-white/5 bg-gradient-to-r from-brand.surface/90 via-slate-950/95 to-brand.surface/90 backdrop-blur-xl shadow-glass">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-tr from-brand.orange via-brand.blue to-brand.orange shadow-lg shadow-brand.orange/40">
                                <span class="h-6 w-6 rounded-full border border-white/40 bg-slate-950/60 shadow-inner"></span>
                            </span>
                            <div>
                                <div class="text-xs uppercase tracking-[0.25em] text-slate-400">
                                    MySportsApp Suite
                                </div>
                                <div class="text-lg md:text-xl font-semibold text-white">
                                    <?php
                                    $title = 'Dashboard';
                                    if ($currentRoute === 'finance' || str_starts_with($currentRoute, 'finance_')) {
                                        $title = 'Finance & Payouts';
                                    } elseif ($currentRoute === 'tickets') {
                                        $title = 'Support Tickets';
                                    } elseif (str_starts_with($currentRoute, 'knowledge')) {
                                        $title = 'Knowledge Base';
                                    } elseif ($currentRoute === 'analytics_sources') {
                                        $title = 'Analytics Sources';
                                    }
                                    echo htmlspecialchars($title);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <?php if ($user): ?>
                            <div class="hidden sm:flex flex-col items-end leading-tight">
                                <span class="text-xs text-slate-400">Signed in as</span>
                                <span class="text-sm font-medium"><?= htmlspecialchars($user['name']) ?></span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-800/70 text-slate-300 border border-white/5">
                                    <?= htmlspecialchars($roleLabel) ?>
                                </span>
                            </div>
                            <form method="post" action="/index.php?route=logout" class="ml-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1 rounded-full border border-red-500/60 bg-red-500/10 px-3 py-1 text-xs font-medium text-red-200 hover:bg-red-500/20 transition">
                                    <span>Logout</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gradient-to-b from-transparent via-brand.bg/80 to-black">
            <div class="mx-auto max-w-7xl px-4 py-6 md:px-8 md:py-8">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-province]').forEach(btn => {
        btn.addEventListener('click', () => {
            const code = btn.getAttribute('data-toggle-province');
            const target = document.querySelector('[data-province-rows="' + code + '"]');
            if (!target) return;
            const icon = btn.querySelector('[data-chevron]');
            const isHidden = target.classList.contains('hidden');
            target.classList.toggle('hidden');
            if (icon) {
                icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
    });
});
</script>
</body>
</html>
