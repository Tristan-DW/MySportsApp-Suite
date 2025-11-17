<?php ob_start(); ?>
<div class="flex items-center justify-between mb-4">
    <div>
        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Tickets</div>
        <div class="text-sm text-slate-200 mt-1">Incoming support tickets from clubs, regions, and players.</div>
    </div>
</div>

<div class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass">
    <table class="min-w-full text-xs text-slate-200">
        <thead class="bg-slate-900/90">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">Subject</th>
                <th class="px-3 py-2 text-left font-semibold">From</th>
                <th class="px-3 py-2 text-left font-semibold">Status</th>
                <th class="px-3 py-2 text-left font-semibold">Priority</th>
                <th class="px-3 py-2 text-left font-semibold">Created</th>
                <th class="px-3 py-2 text-right font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80">
        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="6" class="px-3 py-4 text-center text-slate-500">No tickets yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $t): ?>
                <tr class="hover:bg-slate-900/70">
                    <td class="px-3 py-2">
                        <div class="font-medium text-slate-100"><?= htmlspecialchars($t['subject']) ?></div>
                    </td>
                    <td class="px-3 py-2 text-slate-300">
                        <?= htmlspecialchars($t['public_name'] ?: '-') ?>
                        <?php if (!empty($t['public_email'])): ?>
                            <span class="text-slate-500">· <?= htmlspecialchars($t['public_email']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                            <?= $t['status'] === 'resolved'
                                ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40'
                                : ($t['status'] === 'pending'
                                    ? 'bg-amber-500/15 text-amber-300 border border-amber-500/40'
                                    : 'bg-red-500/15 text-red-300 border border-red-500/40') ?>">
                            <?= htmlspecialchars(ucfirst($t['status'])) ?>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-slate-300">
                        <?= htmlspecialchars(ucfirst($t['priority'])) ?>
                    </td>
                    <td class="px-3 py-2 text-slate-400">
                        <?= htmlspecialchars($t['created_at']) ?>
                    </td>
                    <td class="px-3 py-2 text-right">
                        <a href="/index.php?route=ticket_view&id=<?= (int)$t['id'] ?>"
                           class="text-xs text-sky-400 hover:text-orange-400">Open</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
