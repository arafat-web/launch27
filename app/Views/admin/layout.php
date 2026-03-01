<?php
// Admin layout — wraps all admin pages except login
$isLogin = ($template ?? '') === 'admin/login';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function aActive(string $seg, string $current): string {
    return str_contains($current, $seg) ? 'active' : '';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($_pageTitle ?? 'Admin — Clean27') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/public/css/admin.css">
<meta name="robots" content="noindex, nofollow">
</head>
<body>

<?php if ($isLogin): ?>
<?= $content ?>
<?php else: ?>

<div class="admin-shell">

    <!-- ── SIDEBAR ─────────────────────────────────────────── -->
    <nav class="admin-nav">
        <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin" class="nav-brand">
            <div class="nav-brand-icon">C</div>
            <div class="nav-brand-text">Clean27<small>Admin Panel</small></div>
        </a>

        <div class="nav-section">
            <div class="nav-section-label">Main</div>
            <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin" class="nav-link <?= aActive('/admin', $currentPath) && !str_contains($currentPath, '/admin/') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i><span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Site Management</div>
            <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/seo" class="nav-link <?= aActive('/admin/seo', $currentPath) ?>">
                <i class="fa-solid fa-magnifying-glass-chart"></i><span>SEO Settings</span>
            </a>
            <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/content" class="nav-link <?= aActive('/admin/content', $currentPath) ?>">
                <i class="fa-solid fa-file-pen"></i><span>Site Content</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Account</div>
            <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/settings" class="nav-link <?= aActive('/admin/settings', $currentPath) ?>">
                <i class="fa-solid fa-gear"></i><span>Settings</span>
            </a>
        </div>

        <div class="nav-footer">
            <div class="nav-user">
                <div class="nav-avatar"><?= strtoupper(substr($_user['username'] ?? 'A', 0, 1)) ?></div>
                <span class="nav-username"><?= htmlspecialchars($_user['username'] ?? '') ?></span>
            </div>
            <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/logout" class="nav-logout">
                <i class="fa-solid fa-right-from-bracket"></i><span>Log Out</span>
            </a>
        </div>
    </nav>

    <!-- ── MAIN ────────────────────────────────────────────── -->
    <div class="admin-main">
        <div class="admin-topbar">
            <span class="topbar-title"><?= htmlspecialchars($_pageTitle ?? 'Admin') ?></span>
            <div class="topbar-right">
                <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/" target="_blank" class="preview-btn">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View Site
                </a>
            </div>
        </div>
        <div class="admin-body">
            <?= $content ?>
        </div>
    </div>

</div>

<?php endif; ?>
</body>
</html>
