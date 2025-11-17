<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Support – MySportsApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-black text-slate-100">
<div class="max-w-xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-sky-400 via-orange-400 to-sky-400 flex items-center justify-center shadow-lg">
                <span class="text-xs font-bold tracking-tight text-slate-950">MS</span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-[0.25em] text-slate-500">MySportsApp</div>
                <div class="text-lg font-semibold text-white">Support</div>
            </div>
        </div>
        <a href="/index.php?route=faq" class="text-xs text-sky-400 hover:text-orange-400">Back to FAQ</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-4 rounded-xl border border-red-500/40 bg-red-500/10 text-red-200 text-sm px-3 py-2">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="mb-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-emerald-200 text-sm px-3 py-2">
            Thank you. Your request has been submitted. Our support team will contact you.
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-4 rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-5">
        <div class="text-sm text-slate-300 mb-2">
            Fill in the form below. Use the same email you use in the app so we can find your profile.
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Full name</label>
            <input type="text" name="name" required
                   class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
            <input type="email" name="email" required
                   class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Subject</label>
            <input type="text" name="subject" required
                   class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Message</label>
            <textarea name="message" rows="5" required
                      class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
        </div>

        <button type="submit"
                class="w-full inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-sm font-semibold px-4 py-2 shadow-lg">
            Submit Ticket
        </button>
    </form>

    <div class="mt-6 text-xs text-slate-500 text-center">
        Admin area: <a href="/index.php?route=login" class="text-sky-400 hover:text-orange-400">Login here</a>
    </div>
</div>
</body>
</html>
