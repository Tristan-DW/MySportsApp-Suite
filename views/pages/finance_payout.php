<?php ob_start(); ?>
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Payout Breakdown</div>
            <div class="text-sm text-slate-200 mt-1">
                Settlement #<?= htmlspecialchars($settlement['paystack_id']) ?>
            </div>
        </div>
        <a href="/index.php?route=finance" class="text-xs text-sky-400 hover:text-orange-400">Back to payouts</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">National Total</div>
            <div class="mt-1 text-lg font-semibold text-orange-300">
                R <?= number_format($nationalTotal, 2) ?>
            </div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Transactions</div>
            <div class="mt-1 text-lg font-semibold text-slate-100">
                <?= count($transactions) ?>
            </div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</div>
            <div class="mt-1 text-lg font-semibold text-emerald-300">
                <?= htmlspecialchars(ucfirst($settlement['status'])) ?>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass mt-2">
        <div class="px-4 py-3 border-b border-slate-800/80">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">By Province & Region</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-slate-200">
                <thead class="bg-slate-900/90">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Province / Region</th>
                        <th class="px-3 py-2 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                <?php if (empty($provinces)): ?>
                    <tr>
                        <td colspan="2" class="px-3 py-4 text-center text-slate-500">
                            No transaction breakdown available for this payout.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($provinces as $p): ?>
                        <tr class="bg-slate-950/80">
                            <td class="px-3 py-2 font-semibold text-slate-100">
                                <?= htmlspecialchars($p['province']) ?>
                            </td>
                            <td class="px-3 py-2 text-right text-orange-300">
                                R <?= number_format($p['total'] / 100.0, 2) ?>
                            </td>
                        </tr>
                        <?php foreach ($p['regions'] as $r): ?>
                            <tr class="hover:bg-slate-900/70">
                                <td class="px-3 py-1 pl-8 text-slate-300">
                                    <?= htmlspecialchars($r['region']) ?>
                                </td>
                                <td class="px-3 py-1 text-right text-slate-200">
                                    R <?= number_format($r['total'] / 100.0, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
