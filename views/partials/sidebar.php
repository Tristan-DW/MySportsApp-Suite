<?php
$user = current_user();
$currentRoute = $_GET['route'] ?? 'dashboard';

function nav_active(string $route, array $aliases = []): string {
    global $currentRoute;
    $routes = array_merge([$route], $aliases);
    return in_array($currentRoute, $routes, true)
        ? 'bg-slate-800/80 text-white shadow-lg shadow-brand.orange/30 border border-brand.orange/40'
        : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent';
}
?>
<aside class="hidden md:flex md:flex-col w-60 shrink-0 border-r border-white/5 bg-slate-950/95 backdrop-blur-2xl shadow-glass">
    <div class="px-4 pt-5 pb-4 border-b border-white/5">
        <div class="flex items-center gap-2">
            <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-brand.blue via-brand.orange to-brand.blue flex items-center justify-center shadow-lg shadow-brand.blue/40">
                <span class="text-xs font-bold tracking-tight text-slate-950">MS</span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-[0.25em] text-slate-500">Suite</div>
                <div class="text-sm font-semibold text-white leading-tight">Control Center</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1 text-sm">
        <a href="/index.php?route=dashboard"
           class="flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('dashboard') ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">📊</span>
            <span>Dashboard</span>
        </a>

        <?php if ($user && in_array($user['role'], ['super_admin', 'support'], true)): ?>
        <a href="/index.php?route=tickets"
           class="flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('tickets', ['ticket_view']) ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">🎫</span>
            <span>Tickets</span>
        </a>

        <a href="/index.php?route=knowledge"
           class="flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('knowledge', ['knowledge_edit']) ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">📚</span>
            <span>Knowledge Base</span>
        </a>
        <?php endif; ?>

        <?php if ($user && in_array($user['role'], ['super_admin', 'accounting', 'support'], true)): ?>
        <a href="/index.php?route=finance"
           class="flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('finance', ['finance_payout']) ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">💰</span>
            <span>Finance</span>
        </a>
        <?php endif; ?>

        <?php if ($user && $user['role'] === 'super_admin'): ?>
        <div class="pt-3 mt-3 border-t border-white/5 text-[11px] uppercase tracking-[0.2em] text-slate-500">
            System
        </div>

        <a href="/index.php?route=analytics_sources"
           class="mt-1 flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('analytics_sources') ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">🗄️</span>
            <span>Analytics Sources</span>
        </a>

        <a href="/index.php?route=finance_settings"
           class="flex items-center gap-2 rounded-xl px-3 py-2.5 <?= nav_active('finance_settings') ?>">
            <span class="h-6 w-6 inline-flex items-center justify-center rounded-lg bg-slate-900/70">⚙️</span>
            <span>Finance Integrations</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="px-4 py-4 border-t border-white/5 text-[11px] text-slate-500">
        <div class="flex items-center justify-between">
            <span>Dark</span>
            <span class="inline-flex h-7 items-center rounded-full bg-slate-900/80 px-2 text-slate-300 text-[10px]">
                <span class="h-4 w-4 rounded-full bg-gradient-to-br from-brand.blue to-brand.orange mr-1"></span>
                Sports Control
            </span>
        </div>
    </div>
</aside>
