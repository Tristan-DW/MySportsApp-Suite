<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Help Center – MySportsApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-black text-slate-100">
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-sky-400 via-orange-400 to-sky-400 flex items-center justify-center shadow-lg">
                <span class="text-xs font-bold tracking-tight text-slate-950">MS</span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-[0.25em] text-slate-500">MySportsApp</div>
                <div class="text-lg font-semibold text-white">Help Center</div>
            </div>
        </div>
        <a href="/index.php?route=login"
           class="text-xs text-sky-400 hover:text-orange-400">
            Admin Login
        </a>
    </div>

    <div class="mb-4 text-sm text-slate-300">
        Browse the most common questions about registrations, wallets, and the app.
        If you still need help, you can <a href="/index.php?route=ticket_public" class="text-sky-400 hover:text-orange-400">submit a support request</a>.
    </div>

    <?php if (empty($articles)): ?>
        <div class="rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-5 text-sm text-slate-400">
            No FAQs published yet.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($articles as $a): ?>
                <details class="group rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-3">
                    <summary class="flex items-center justify-between cursor-pointer list-none">
                        <div>
                            <?php if (!empty($a['category'])): ?>
                                <div class="text-[10px] uppercase tracking-[0.2em] text-slate-500 mb-0.5">
                                    <?= htmlspecialchars($a['category']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-sm font-semibold text-white">
                                <?= htmlspecialchars($a['title']) ?>
                            </div>
                        </div>
                        <span class="ml-2 text-slate-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="mt-2 text-sm text-slate-200 leading-relaxed">
                        <?= nl2br(htmlspecialchars($a['content'])) ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
