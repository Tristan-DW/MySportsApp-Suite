<?php ob_start(); ?>
<div class="flex items-center justify-between mb-4">
    <div>
        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Knowledge Base</div>
        <div class="text-sm text-slate-200 mt-1">Manage internal articles and public FAQs.</div>
    </div>
    <a href="/index.php?route=knowledge_edit"
       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-xs font-semibold px-3 py-2 shadow-md">
        <span>+ New Article</span>
    </a>
</div>

<div class="rounded-2xl border border-white/5 bg-slate-950/80 backdrop-blur-xl shadow-glass">
    <table class="min-w-full text-xs text-slate-200">
        <thead class="bg-slate-900/90">
            <tr>
                <th class="px-3 py-2 text-left font-semibold">Title</th>
                <th class="px-3 py-2 text-left font-semibold">Category</th>
                <th class="px-3 py-2 text-center font-semibold">Public</th>
                <th class="px-3 py-2 text-left font-semibold">Updated</th>
                <th class="px-3 py-2 text-right font-semibold">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80">
        <?php if (empty($articles)): ?>
            <tr>
                <td colspan="5" class="px-3 py-4 text-center text-slate-500">No articles yet.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($articles as $a): ?>
                <tr class="hover:bg-slate-900/70">
                    <td class="px-3 py-2">
                        <div class="font-medium text-slate-100"><?= htmlspecialchars($a['title']) ?></div>
                    </td>
                    <td class="px-3 py-2 text-slate-300">
                        <?= htmlspecialchars($a['category'] ?: '-') ?>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <?php if ($a['is_public']): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 text-[11px] border border-emerald-500/40">
                                Public
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-800/80 text-slate-400 text-[11px] border border-slate-600/40">
                                Internal
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-2 text-slate-400">
                        <?= htmlspecialchars($a['updated_at']) ?>
                    </td>
                    <td class="px-3 py-2 text-right">
                        <a href="/index.php?route=knowledge_edit&id=<?= (int)$a['id'] ?>"
                           class="text-xs text-sky-400 hover:text-orange-400">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
