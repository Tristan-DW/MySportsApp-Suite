<?php
/** @var array $metrics */
/** @var array $provinces */

ob_start();
?>
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <?php
        $cards = [
            ['label' => 'Total Players', 'key' => 'players', 'emoji' => '⚽', 'accent' => 'from-brand.blue to-brand.orange'],
            ['label' => 'Total Clubs', 'key' => 'clubs', 'emoji' => '🏟️', 'accent' => 'from-emerald-400 to-brand.blue'],
            ['label' => 'Total Coaches', 'key' => 'coaches', 'emoji' => '🧢', 'accent' => 'from-purple-400 to-brand.blue'],
            ['label' => 'Total Referees', 'key' => 'refs', 'emoji' => '🟥', 'accent' => 'from-rose-500 to-orange-400'],
            ['label' => 'REOs', 'key' => 'reos', 'emoji' => '🏛️', 'accent' => 'from-yellow-400 to-orange-500'],
        ];
        ?>

        <?php foreach ($cards as $card): ?>
            <div class="relative overflow-hidden rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass">
                <div class="absolute inset-0 bg-gradient-to-br <?= $card['accent'] ?> opacity-10"></div>
                <div class="relative p-4 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                            <?= htmlspecialchars($card['label']) ?>
                        </div>
                        <div class="h-7 w-7 rounded-xl bg-slate-900/80 flex items-center justify-center text-lg">
                            <?= $card['emoji'] ?>
                        </div>
                    </div>
                    <div class="text-2xl font-semibold text-white">
                        <?= number_format((int)($metrics[$card['key']] ?? 0)) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-1 rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                        Support & Finance
                    </div>
                    <div class="text-sm text-slate-200 mt-1">
                        Operational snapshots (last 30 days)
                    </div>
                </div>
                <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-brand.orange to-brand.blue flex items-center justify-center text-xl">
                    📈
                </div>
            </div>

            <div class="mt-2 space-y-3">
                <div class="flex items-center justify-between rounded-xl bg-slate-900/80 px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="h-7 w-7 flex items-center justify-center rounded-lg bg-red-500/15 text-red-300 text-sm">🎫</span>
                        <div>
                            <div class="text-xs text-slate-400">Open Tickets</div>
                            <div class="text-sm font-medium text-white">
                                <?= number_format((int)($metrics['openTickets'] ?? 0)) ?>
                            </div>
                        </div>
                    </div>
                    <a href="/index.php?route=tickets" class="text-xs text-brand.blue hover:text-brand.orange">View</a>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-slate-900/80 px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="h-7 w-7 flex items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-300 text-sm">💸</span>
                        <div>
                            <div class="text-xs text-slate-400">Income (30 days)</div>
                            <div class="text-sm font-medium text-emerald-300">
                                R <?= number_format((float)($metrics['last30Income'] ?? 0), 2) ?>
                            </div>
                        </div>
                    </div>
                    <a href="/index.php?route=finance" class="text-xs text-brand.blue hover:text-brand.orange">Finance</a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                        Geography
                    </div>
                    <div class="text-sm text-slate-200 mt-1">
                        Province → Region breakdown (all analytics DBs)
                    </div>
                </div>
                <span class="text-[11px] px-2 py-1 rounded-full bg-slate-900/80 text-slate-400 border border-white/5">
                    Live from <?= count(analytics_dbs()) ?> DB<?= count(analytics_dbs()) === 1 ? '' : 's' ?>
                </span>
            </div>

            <?php if (empty($provinces)): ?>
                <div class="text-sm text-slate-400">
                    No analytics data yet. Configure analytics sources and sync your sports DBs.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto rounded-xl border border-white/5">
                    <table class="min-w-full text-xs text-slate-200">
                        <thead class="bg-slate-900/90">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Province / Region</th>
                                <th class="px-3 py-2 text-right font-semibold">Members</th>
                                <th class="px-3 py-2 text-right font-semibold">Players</th>
                                <th class="px-3 py-2 text-right font-semibold">Coaches</th>
                                <th class="px-3 py-2 text-right font-semibold">Referees</th>
                                <th class="px-3 py-2 text-right font-semibold">REOs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 bg-slate-950/60">
                        <?php foreach ($provinces as $province): ?>
                            <tr class="hover:bg-slate-900/70 transition cursor-pointer"
                                data-toggle-province="<?= htmlspecialchars($province['code']) ?>">
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <span data-chevron class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-900/80 text-[10px] text-slate-300 transition-transform">
                                            ▼
                                        </span>
                                        <span class="font-medium text-slate-100">
                                            <?= htmlspecialchars($province['name']) ?>
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full border border-slate-700 text-slate-400">
                                            <?= htmlspecialchars($province['code']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-right"><?= number_format($province['members']) ?></td>
                                <td class="px-3 py-2 text-right text-sky-300"><?= number_format($province['players']) ?></td>
                                <td class="px-3 py-2 text-right text-amber-300"><?= number_format($province['coaches']) ?></td>
                                <td class="px-3 py-2 text-right text-rose-300"><?= number_format($province['referees']) ?></td>
                                <td class="px-3 py-2 text-right text-emerald-300"><?= number_format($province['reos']) ?></td>
                            </tr>

                            <tr class="bg-slate-950 hidden" data-province-rows="<?= htmlspecialchars($province['code']) ?>">
                                <td colspan="6" class="px-3 py-0">
                                    <div class="border-t border-slate-800/70 mt-1 pt-1">
                                        <?php if (empty($province['regions'])): ?>
                                            <div class="text-[11px] text-slate-500 px-2 py-2">
                                                No regions found for this province.
                                            </div>
                                        <?php else: ?>
                                            <table class="w-full text-[11px] text-slate-300">
                                                <tbody>
                                                <?php foreach ($province['regions'] as $region): ?>
                                                    <tr class="hover:bg-slate-900/80 transition">
                                                        <td class="pl-9 pr-2 py-1">
                                                            <span class="text-slate-300"><?= htmlspecialchars($region['name']) ?></span>
                                                        </td>
                                                        <td class="px-2 py-1 text-right"><?= number_format($region['members']) ?></td>
                                                        <td class="px-2 py-1 text-right text-sky-300"><?= number_format($region['players']) ?></td>
                                                        <td class="px-2 py-1 text-right text-amber-300"><?= number_format($region['coaches']) ?></td>
                                                        <td class="px-2 py-1 text-right text-rose-300"><?= number_format($region['referees']) ?></td>
                                                        <td class="px-2 py-1 text-right text-emerald-300"><?= number_format($region['reos']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layout.php';
