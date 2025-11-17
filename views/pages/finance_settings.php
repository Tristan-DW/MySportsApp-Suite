<?php ob_start(); ?>
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Finance Integrations</div>
            <div class="text-sm text-slate-200 mt-1">Connect Paystack and Xero for live payouts and accounting.</div>
        </div>
    </div>

    <?php if (!empty($saved)): ?>
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-emerald-200 text-sm px-3 py-2">
            Settings saved.
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-6">
        <div class="rounded-2xl border border-white/5 bg-slate-950/80 p-4 shadow-glass space-y-3">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Paystack</div>
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Secret Key</label>
                    <input type="password" name="paystack_secret_key"
                           value="<?= htmlspecialchars($paystack_secret_key) ?>"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Public Key</label>
                    <input type="text" name="paystack_public_key"
                           value="<?= htmlspecialchars($paystack_public_key) ?>"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/5 bg-slate-950/80 p-4 shadow-glass space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Xero</div>
                    <div class="text-xs text-slate-300 mt-1">
                        Configure OAuth credentials. The actual OAuth flow still needs to be wired with your tenant.
                    </div>
                </div>
                <button type="button"
                        class="text-[11px] px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/40 cursor-not-allowed">
                    Connect Xero (stub)
                </button>
            </div>
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Client ID</label>
                    <input type="text" name="xero_client_id"
                           value="<?= htmlspecialchars($xero_client_id) ?>"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Client Secret</label>
                    <input type="password" name="xero_client_secret"
                           value="<?= htmlspecialchars($xero_client_secret) ?>"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">Redirect URI</label>
                    <input type="text" name="xero_redirect_uri"
                           value="<?= htmlspecialchars($xero_redirect_uri) ?>"
                           class="w-full rounded-xl bg-slate-900 border border-slate-700 px-3 py-2 text-xs text-white" />
                </div>
            </div>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-400 to-sky-400 text-slate-950 text-xs font-semibold px-4 py-2 shadow-md">
            Save Settings
        </button>
    </form>
</div>
<?php $content = ob_get_clean(); include BASE_PATH . '/views/layout.php'; ?>
