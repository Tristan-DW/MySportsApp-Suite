<?php ob_start(); ?>
<div class="max-w-xl mx-auto">
    <div class="rounded-2xl border border-white/5 bg-slate-950/80 p-4 shadow-glass">
        <?php if (!empty($ok)): ?>
            <div class="text-sm text-emerald-300">
                Paystack sync completed successfully. Check the Payouts table for new data.
            </div>
        <?php else: ?>
            <div class="text-sm text-red-300">
                Paystack sync failed: <?= htmlspecialchars($error ?? 'Unknown error') ?>
            </div>
        <?php endif; ?>
        <div class="mt-3">
            <a href="/index.php?route=finance" class="text-xs text-sky-400 hover:text-orange-400">Back to Finance</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
