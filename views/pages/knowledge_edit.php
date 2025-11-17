<?php
$editing = !empty($article);
ob_start();
?>
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">
                <?= $editing ? 'Edit Article' : 'New Article' ?>
            </div>
            <div class="text-sm text-slate-200 mt-1">
                <?= $editing ? 'Update knowledge base content.' : 'Create a new knowledge base article.' ?>
            </div>
        </div>
        <a href="/index.php?route=knowledge" class="text-xs text-sky-400 hover:text-orange-400">Back to list</a>
    </div>

    <form method="post" class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Title</label>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                   class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Category</label>
            <input type="text" name="category"
                   value="<?= htmlspecialchars($article['category'] ?? '') ?>"
                   class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-400 mb-1.5">Content</label>
            <textarea name="content" rows="8" required
                      class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500"><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs text-slate-300">
                <input type="checkbox" name="is_public" value="1"
                       <?= !empty($article['is_public']) ? 'checked' : '' ?>
                       class="rounded border-slate-600 bg-slate-900 text-orange-400" />
                <span>Make article public (FAQ)</span>
            </label>

            <?php if ($editing): ?>
            <button type="submit" name="delete" value="1"
                    onclick="return confirm('Delete this article?');"
                    class="text-xs text-red-300 hover:text-red-200">
                Delete
            </button>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-xs font-semibold px-4 py-2 shadow-md">
            <span><?= $editing ? 'Save Changes' : 'Create Article' ?></span>
        </button>
    </form>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
