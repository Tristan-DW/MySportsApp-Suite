<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Login – MySportsApp Suite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-black text-slate-100">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-slate-950/90 border border-white/10 rounded-3xl shadow-2xl shadow-black/70 p-8 backdrop-blur-xl">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-sky-400 via-orange-400 to-sky-400 flex items-center justify-center shadow-lg">
                <span class="text-xs font-bold tracking-tight text-slate-950">MS</span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-[0.25em] text-slate-500">MySportsApp</div>
                <div class="text-lg font-semibold text-white">Suite Login</div>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 text-red-200 text-sm px-3 py-2">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/index.php?route=login_submit" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                <input type="email" name="email" required
                       value="<?= htmlspecialchars($email ?? '') ?>"
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-orange-500" />
            </div>

            <button type="submit"
                    class="w-full inline-flex justify-center items-center rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-sm font-semibold py-2.5 shadow-lg hover:brightness-110 transition">
                Sign in
            </button>
        </form>

        <div class="mt-6 text-xs text-slate-500 text-center">
            Public FAQ: <a href="/index.php?route=faq" class="text-sky-400 hover:text-orange-400">View here</a> ·
            Submit ticket: <a href="/index.php?route=ticket_public" class="text-sky-400 hover:text-orange-400">Support form</a>
        </div>
    </div>
</div>
</body>
</html>
