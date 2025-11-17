<?php ob_start(); ?>
<div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Income</div>
            <div class="mt-1 text-lg font-semibold text-emerald-300">
                R <?= number_format($totalIncome, 2) ?>
            </div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Payouts</div>
            <div class="mt-1 text-lg font-semibold text-orange-300">
                R <?= number_format($totalPayouts, 2) ?>
            </div>
        </div>
        <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-slate-950/80 px-4 py-3 shadow-glass">
            <div>
                <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Net</div>
                <div class="mt-1 text-lg font-semibold text-slate-100">
                    R <?= number_format($totalIncome - $totalPayouts, 2) ?>
                </div>
            </div>
            <?php $user = current_user(); if ($user && $user['role'] === 'super_admin'): ?>
                <a href="/index.php?route=finance_sync_paystack"
                   class="text-[11px] px-3 py-1 rounded-full bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 font-semibold shadow-md">
                    Sync Paystack
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass mt-2">
        <div class="px-4 py-3 border-b border-slate-800/80 flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Payouts</div>
                <div class="text-sm text-slate-200 mt-1">Settlements from Paystack</div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-slate-200">
                <thead class="bg-slate-900/90">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">ID</th>
                        <th class="px-3 py-2 text-left font-semibold">Status</th>
                        <th class="px-3 py-2 text-left font-semibold">Settled At</th>
                        <th class="px-3 py-2 text-right font-semibold">Amount</th>
                        <th class="px-3 py-2 text-right font-semibold">Volume</th>
                        <th class="px-3 py-2 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                <?php if (empty($settlements)): ?>
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-slate-500">
                            No settlements yet. Configure Paystack and sync.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($settlements as $s): ?>
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-3 py-2 text-slate-300">
                                <?= (int)$s['paystack_id'] ?>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                    <?= $s['status'] === 'success'
                                        ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40'
                                        : 'bg-slate-800/80 text-slate-300 border border-slate-600/40' ?>">
                                    <?= htmlspecialchars(ucfirst($s['status'])) ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-slate-400">
                                <?= htmlspecialchars($s['settled_at'] ?: $s['paid_at']) ?>
                            </td>
                            <td class="px-3 py-2 text-right text-orange-300">
                                R <?= number_format($s['total_amount'] / 100.0, 2) ?>
                            </td>
                            <td class="px-3 py-2 text-right text-slate-300">
                                <?= number_format((int)$s['transaction_volume']) ?>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="/index.php?route=finance_payout&id=<?= (int)$s['id'] ?>"
                                   class="text-xs text-sky-400 hover:text-orange-400">View Breakdown</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
