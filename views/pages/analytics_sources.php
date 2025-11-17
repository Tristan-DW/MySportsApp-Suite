<?php ob_start(); ?>
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Analytics Sources</div>
            <div class="text-sm text-slate-200 mt-1">
                Connect one or more sports databases (same schema) for the analytics dashboard.
            </div>
        </div>
    </div>

    <form method="post"
          class="rounded-2xl border border-white/5 bg-slate-950/80 p-4 shadow-glass space-y-3">
        <input type="hidden" name="id" value="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">Name</label>
                <input type="text" name="name" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">DSN</label>
                <input type="text" name="dsn" required
                       placeholder="mysql:host=...;dbname=..."
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">DB User</label>
                <input type="text" name="db_user" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">DB Password</label>
                <input type="password" name="db_password" required
                       class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
            </div>
        </div>
        <label class="flex items-center gap-2 text-xs text-slate-300 mt-1">
            <input type="checkbox" name="is_active" value="1" checked
                   class="rounded border-slate-600 bg-slate-900 text-orange-400" />
            <span>Active</span>
        </label>
        <button type="submit"
                class="mt-2 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-xs font-semibold px-4 py-2 shadow-md">
            Add Source
        </button>
    </form>

    <div class="rounded-2xl border border-white/5 bg-slate-950/80 p-4 shadow-glass">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Existing Sources</div>
        <div class="space-y-2">
            <?php if (empty($sources)): ?>
                <div class="text-[11px] text-slate-500">No sources configured.</div>
            <?php else: ?>
                <?php foreach ($sources as $s): ?>
                    <div class="flex items-center justify-between rounded-xl bg-slate-900/80 px-3 py-2 text-xs">
                        <div>
                            <div class="font-semibold text-slate-100"><?= htmlspecialchars($s['name']) ?></div>
                            <div class="text-slate-400"><?= htmlspecialchars($s['dsn']) ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-slate-300"><?= htmlspecialchars($s['db_user']) ?></div>
                            <div class="text-[10px] mt-1">
                                <?= $s['is_active'] ? 'Active' : 'Disabled' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
