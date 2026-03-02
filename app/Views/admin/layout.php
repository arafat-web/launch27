<?php
// Admin layout — wraps all admin pages except login
$isLogin = ($template ?? '') === 'admin/login';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function aActive(string $seg, string $current): string
{
    return str_contains($current, $seg) ? 'active' : '';
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($_pageTitle ?? 'Admin — BronxHomeServices') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/public/css/admin.css">
    <meta name="robots" content="noindex, nofollow">
</head>

<body>

    <?php if ($isLogin): ?>
        <?= $content ?>
    <?php else: ?>

        <div class="admin-shell">

            <!-- ── SIDEBAR OVERLAY (mobile) ──────────────────── -->
            <div class="nav-overlay" id="navOverlay" onclick="closeNav()"></div>

            <!-- ── SIDEBAR ─────────────────────────────────────── -->
            <nav class="admin-nav" id="adminNav">
                <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin" class="nav-brand">
                    <div class="nav-brand-icon">C</div>
                    <div class="nav-brand-text">BronxHomeServices<small>Admin Panel</small></div>
                </a>

                <div class="nav-section">
                    <div class="nav-section-label">Main</div>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin"
                        class="nav-link <?= aActive('/admin', $currentPath) && !str_contains($currentPath, '/admin/') ? 'active' : '' ?>">
                        <i class="fa-solid fa-gauge"></i><span>Dashboard</span>
                    </a>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/bookings"
                        class="nav-link <?= aActive('/admin/bookings', $currentPath) ?>">
                        <i class=" fa-solid fa-calendar-check"></i><span>Bookings</span> </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-label">Site Management</div>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/seo"
                        class="nav-link <?= aActive('/admin/seo', $currentPath) ?>">
                        <i class=" fa-solid fa-magnifying-glass-chart"></i><span>SEO Settings</span>
                    </a>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/content"
                        class="nav-link <?= aActive('/admin/content', $currentPath) ?>">
                        <i class="fa-solid fa-file-pen"></i><span>Site Content</span>
                    </a>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/visitors"
                        class="nav-link <?= aActive('/admin/visitors', $currentPath) ?>">
                        <i class="fa-solid fa-users"></i><span>Visitors</span>
                    </a>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/logs"
                        class="nav-link <?= aActive('/admin/logs', $currentPath) ?>">
                        <i class="fa-solid fa-terminal"></i><span>API Logs</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-label">Account</div>
                    <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/settings"
                        class="nav-link <?= aActive('/admin/settings', $currentPath) ?>">
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

            <!-- ── MAIN ────────────────────────────────────────── -->
            <div class="admin-main">
                <div class="admin-topbar">
                    <!-- Hamburger (mobile only) -->
                    <button class="mob-menu-btn" id="mobMenuBtn" onclick="openNav()" aria-label="Open menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <span class="topbar-title"><?= htmlspecialchars($_pageTitle ?? 'Admin') ?></span>
                    <div class="topbar-right">
                        <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/" target="_blank"
                            class="preview-btn">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>View Site</span>
                        </a>
                    </div>
                </div>
                <div class="admin-body">
                    <?= $content ?>
                </div>
            </div>

        </div>

    <?php endif; ?>

    <script>
        function openNav() {
            document.getElementById('adminNav').classList.add('open');
            document.getElementById('navOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeNav() {
            document.getElementById('adminNav').classList.remove('open');
            document.getElementById('navOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
        // Close on nav-link click (navigating away on mobile)
        document.querySelectorAll('.nav-link').forEach(function (el) {
            el.addEventListener('click', function () { closeNav(); });
        });
    </script>
</body>

</html>