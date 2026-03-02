<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$statusColors = [
    'SUCCESS' => '#10B981',
    'ERROR' => '#EF4444',
    'INFO' => '#3B82F6',
    'WARN' => '#F59E0B',
];
?>

<div class="page-hdr" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
        <h1><i class="fa-solid fa-terminal" style="color:var(--blue);margin-right:8px;"></i>API Logs</h1>
        <p>Real-time booking API call history. Latest 200 entries shown.</p>
    </div>
</div>

<!-- ── FILE SELECTOR ─────────────────────────────────────────────────────── -->
<?php if (count($logFiles) > 1): ?>
    <div class="visitors-filters" style="margin-bottom:20px;">
        <?php foreach ($logFiles as $f): ?>
            <a href="<?= $base ?>/admin/logs?file=<?= urlencode($f['name']) ?>"
                class="vf-btn<?= $selectedFile === $f['name'] ? ' active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i>
                <?= htmlspecialchars($f['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ── LOG ENTRIES ───────────────────────────────────────────────────────── -->
<div class="card">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-list"></i>
                <?= htmlspecialchars($selectedFile ?: 'No log file selected') ?>
            </h2>
            <p>
                <?= count($entries) ?> entries
            </p>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($entries)): ?>
            <div style="padding:40px;text-align:center;color:var(--muted);">
                <i class="fa-solid fa-folder-open" style="font-size:2rem;display:block;margin-bottom:12px;opacity:.35;"></i>
                <p style="margin:0;">No log entries found.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <?php foreach ($entries as $e): ?>
                    <?php
                    $status = $e['status'] ?? 'INFO';
                    $sc = $statusColors[$status] ?? '#6B7280';
                    $type = $e['type'] ?? 'LOG';
                    $ts = $e['timestamp'] ?? '';
                    $ip = $e['ip'] ?? '';
                    $data = $e['data'] ?? $e['raw'] ?? null;
                    ?>
                    <div class="log-entry">
                        <div class="log-meta">
                            <span class="log-status"
                                style="background:<?= $sc ?>1a;color:<?= $sc ?>;border:1px solid <?= $sc ?>44;">
                                <?= htmlspecialchars($status) ?>
                            </span>
                            <span class="log-type">
                                <?= htmlspecialchars($type) ?>
                            </span>
                            <?php if ($ts): ?><span class="log-ts">
                                    <?= htmlspecialchars(date('M j, H:i:s', strtotime($ts))) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($ip): ?><span class="log-ip"><code><?= htmlspecialchars($ip) ?></code></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($data): ?>
                            <details class="log-details">
                                <summary>Show data</summary>
                                <pre><?= htmlspecialchars(is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $data) ?></pre>
                            </details>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* ── FILE SELECTOR TABS ────────────────────────────────────────────────── */
    .visitors-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .vf-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: var(--r);
        background: var(--bg3);
        border: 1px solid var(--border);
        color: var(--muted);
        text-decoration: none;
        font-size: .82rem;
        font-weight: 600;
        transition: all .2s;
    }

    .vf-btn:hover {
        color: var(--text);
        border-color: var(--blue);
        background: rgba(59, 130, 246, 0.08);
    }

    .vf-btn.active {
        background: var(--blue);
        color: var(--white);
        border-color: var(--blue);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    .vf-btn i {
        font-size: .9rem;
    }

    /* ── ENTRIES ───────────────────────────────────────────────────────────── */
    .log-entry {
        border-bottom: 1px solid var(--border);
        padding: 10px 20px;
        transition: background .12s;
    }

    .log-entry:last-child {
        border-bottom: none;
    }

    .log-entry:hover {
        background: var(--bg3);
    }

    .log-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        font-size: .78rem;
    }

    .log-status {
        padding: 2px 8px;
        border-radius: 20px;
        font-size: .68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .log-type {
        font-weight: 600;
        color: var(--white);
        font-size: .8rem;
    }

    .log-ts {
        color: var(--muted);
    }

    .log-ip code {
        background: var(--bg3);
        padding: 1px 5px;
        border-radius: 4px;
        font-size: .72rem;
    }

    .log-details {
        margin-top: 6px;
    }

    .log-details summary {
        cursor: pointer;
        color: var(--muted);
        font-size: .74rem;
        display: inline;
    }

    .log-details pre {
        margin: 8px 0 0;
        padding: 10px 14px;
        background: var(--bg3);
        border-radius: 8px;
        font-size: .72rem;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
        max-height: 240px;
        overflow-y: auto;
        color: var(--text);
    }
</style>