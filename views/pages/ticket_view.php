<?php ob_start(); ?>
<?php if (!$ticket): ?>
    <div class="text-sm text-red-300">Ticket not found.</div>
<?php else: ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass p-4 space-y-3">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Ticket</div>
            <div class="text-sm text-slate-200 mt-1"><?= htmlspecialchars($ticket['subject']) ?></div>
        </div>

        <div class="text-xs text-slate-400">
            From: <span class="text-slate-200"><?= htmlspecialchars($ticket['public_name'] ?: '-') ?></span>
            <?php if (!empty($ticket['public_email'])): ?>
                · <span class="text-slate-300"><?= htmlspecialchars($ticket['public_email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="text-sm text-slate-200 whitespace-pre-line bg-slate-900/80 rounded-xl px-3 py-2">
            <?= htmlspecialchars($ticket['message']) ?>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-4">
        <form method="post"
              class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass p-4 space-y-3">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status & Priority</div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Status</label>
                <select name="status"
                        class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white">
                    <?php foreach (['open','pending','resolved'] as $status): ?>
                        <option value="<?= $status ?>" <?= $ticket['status'] === $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Priority</label>
                <select name="priority"
                        class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white">
                    <?php foreach (['low','normal','high','urgent'] as $p): ?>
                        <option value="<?= $p ?>" <?= $ticket['priority'] === $p ? 'selected' : '' ?>>
                            <?= ucfirst($p) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">Internal note</label>
                <textarea name="note" rows="3"
                          class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white"></textarea>
            </div>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-xs font-semibold px-4 py-2 shadow-md">
                Save
            </button>
        </form>

        <div class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass p-3">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-2">Notes</div>
            <div class="space-y-2 max-h-60 overflow-y-auto">
                <?php if (empty($notes)): ?>
                    <div class="text-[11px] text-slate-500">No notes yet.</div>
                <?php else: ?>
                    <?php foreach ($notes as $n): ?>
                        <div class="rounded-xl bg-slate-900/80 px-3 py-2">
                            <div class="text-[11px] text-slate-400 mb-0.5">
                                <?= htmlspecialchars($n['user_name'] ?? 'System') ?>
                                · <?= htmlspecialchars($n['created_at']) ?>
                            </div>
                            <div class="text-xs text-slate-200 whitespace-pre-line">
                                <?= htmlspecialchars($n['note']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
