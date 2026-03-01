<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$seoCount     = count($seoRows ?? []);
$contentCount = count($contentRows ?? []);
?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-gauge" style="color:var(--blue);margin-right:8px;"></i>Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_user['username'] ?? 'Admin') ?>. Manage your site SEO and content below.</p>
</div>

<!-- Stats row -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
        <div class="stat-val"><?= $seoCount ?></div>
        <div class="stat-lbl">Pages with SEO</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-pen"></i></div>
        <div class="stat-val"><?= $contentCount ?></div>
        <div class="stat-lbl">Content Fields</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
        <div class="stat-val">Live</div>
        <div class="stat-lbl">Site Status</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-database"></i></div>
        <div class="stat-val">SQLite</div>
        <div class="stat-lbl">Database</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
            <p>Jump to the most common tasks</p>
        </div>
    </div>
    <div class="card-body">
        <div class="quick-grid">
            <a href="<?= $base ?>/admin/seo" class="quick-card">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
                <span>Edit SEO</span>
                <small>Page titles & meta descriptions</small>
            </a>
            <a href="<?= $base ?>/admin/content" class="quick-card">
                <i class="fa-solid fa-file-pen"></i>
                <span>Edit Content</span>
                <small>Hero text, stats, CTAs</small>
            </a>
            <a href="<?= $base ?>/admin/settings" class="quick-card">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
                <small>Change admin password</small>
            </a>
            <a href="<?= $base ?>/" target="_blank" class="quick-card">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>View Site</span>
                <small>Opens in a new tab</small>
            </a>
        </div>
    </div>
</div>

<!-- SEO Status -->
<div class="card">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-magnifying-glass-chart"></i> SEO Overview</h2>
            <p>Current state of your page meta tags</p>
        </div>
        <a href="<?= $base ?>/admin/seo" class="btn btn-ghost" style="font-size:.8rem;padding:7px 14px;">Edit SEO</a>
    </div>
    <div class="card-body" style="padding:0;">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:12px 22px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Page</th>
                    <th style="padding:12px 22px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Title</th>
                    <th style="padding:12px 22px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Description</th>
                    <th style="padding:12px 22px;text-align:left;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">Updated</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($seoRows as $row): ?>
                <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background=''">
                    <td style="padding:12px 22px;font-weight:700;text-transform:capitalize;"><?= htmlspecialchars($row['page']) ?></td>
                    <td style="padding:12px 22px;color:<?= $row['title'] ? 'var(--text)' : 'var(--muted)' ?>;">
                        <?= $row['title'] ? htmlspecialchars(mb_strimwidth($row['title'], 0, 50, '…')) : '<em style="opacity:.5">Not set</em>' ?>
                    </td>
                    <td style="padding:12px 22px;color:<?= $row['description'] ? 'var(--text)' : 'var(--muted)' ?>;">
                        <?= $row['description'] ? htmlspecialchars(mb_strimwidth($row['description'], 0, 60, '…')) : '<em style="opacity:.5">Not set</em>' ?>
                    </td>
                    <td style="padding:12px 22px;color:var(--muted);font-size:.78rem;"><?= $row['updated_at'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
