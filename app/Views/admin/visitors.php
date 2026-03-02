<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Country code → flag emoji helper
function countryFlag(string $code): string
{
    if (strlen($code) !== 2)
        return '🌍';
    $code = strtoupper($code);
    return mb_convert_encoding(
        '&#' . (0x1F1E0 + (ord($code[0]) - ord('A'))) . ';&#' . (0x1F1E0 + (ord($code[1]) - ord('A'))) . ';',
        'UTF-8',
        'HTML-ENTITIES'
    );
}

// Label map for filter buttons
$rangeLabels = [
    'today' => 'Today',
    'week' => 'This Week',
    'month' => 'This Month',
    'year' => 'This Year',
    'custom' => 'Custom',
];
?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-users" style="color:var(--blue);margin-right:8px;"></i>Visitors</h1>
    <p>Track every public page visit — location, device, browser and more.</p>
</div>

<!-- ── FILTER BAR ──────────────────────────────────────────────────────────── -->
<div class="visitors-filters">
    <?php foreach (['today', 'week', 'month', 'year'] as $r): ?>
        <a href="<?= $base ?>/admin/visitors?range=<?= $r ?>" class="vf-btn<?= $range === $r ? ' active' : '' ?>">
            <?= $rangeLabels[$r] ?>
        </a>
    <?php endforeach; ?>

    <!-- Custom range form -->
    <form method="GET" action="<?= $base ?>/admin/visitors" class="vf-custom">
        <input type="hidden" name="range" value="custom">
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>"
            class="vf-date<?= $range === 'custom' ? ' active-input' : '' ?>">
        <span class="vf-sep">→</span>
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>"
            class="vf-date<?= $range === 'custom' ? ' active-input' : '' ?>">
        <button type="submit" class="vf-btn<?= $range === 'custom' ? ' active' : '' ?>">
            <i class="fa-solid fa-filter"></i> Apply
        </button>
    </form>
</div>

<!-- ── SUMMARY STATS ──────────────────────────────────────────────────────── -->
<div class="stats-row" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
        <div class="stat-val">
            <?= number_format($stats['total']) ?>
        </div>
        <div class="stat-lbl">Total Visits</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-fingerprint"></i></div>
        <div class="stat-val">
            <?= number_format($stats['unique']) ?>
        </div>
        <div class="stat-lbl">Unique IPs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-earth-americas"></i></div>
        <div class="stat-val" style="font-size:1rem;">
            <?= htmlspecialchars($stats['country'] ?: '—') ?>
        </div>
        <div class="stat-lbl">Top Country</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-val" style="font-size:1rem;text-transform:capitalize;">
            <?= htmlspecialchars($stats['top_page'] ?: '—') ?>
        </div>
        <div class="stat-lbl">Top Page</div>
    </div>
</div>

<?php
// Build chart data
$vtPerDayMap = [];
foreach ($stats['per_day'] ?? [] as $row) {
    $vtPerDayMap[$row['day']] = (int) $row['cnt'];
}
$vtLabels = [];
$vtCnts = [];
for ($i = min(29, count($vtPerDayMap) > 0 ? 29 : 6); $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $vtLabels[] = date('M j', strtotime($d));
    $vtCnts[] = $vtPerDayMap[$d] ?? 0;
}
$vtMax = max(array_merge($vtCnts, [1]));
?>

<!-- ── TRAFFIC CHART ──────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-chart-area" style="color:var(--blue);"></i> Traffic Over Time</h2>
            <p>Visits per day for this period</p>
        </div>
    </div>
    <div class="card-body">
        <canvas id="vtChart" style="width:100%;height:160px;"></canvas>
    </div>
</div>

<!-- ── TOP PAGES + TOP BROWSERS ──────────────────────────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-file-lines" style="color:var(--blue);"></i> Top Pages</h2>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($stats['top_pages'])): ?>
                <div style="padding:20px;color:var(--muted);font-size:.82rem;text-align:center;">No data</div>
            <?php else: ?>
                    <?php foreach ($stats['top_pages'] as $row): ?>
                            <?php $pct = $stats['total'] ? round($row['c'] / $stats['total'] * 100) : 0; ?>
                    <div
                        style="padding:10px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;font-size:.82rem;">
                        <span class="vt-badge"
                            style="min-width:60px;text-align:center;"><?= htmlspecialchars(ucfirst($row['page'] ?: 'home')) ?></span>
                        <div style="flex:1;background:var(--bg3);border-radius:4px;height:6px;overflow:hidden;">
                            <div style="width:<?= $pct ?>%;height:100%;background:var(--blue);border-radius:4px;"></div>
                        </div>
                        <span style="min-width:30px;text-align:right;color:var(--muted);"><?= $row['c'] ?></span>
                    </div>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-globe" style="color:var(--blue);"></i> Top Browsers</h2>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($stats['top_browsers'])): ?>
                <div style="padding:20px;color:var(--muted);font-size:.82rem;text-align:center;">No data</div>
            <?php else: ?>
                    <?php foreach ($stats['top_browsers'] as $row): ?>
                            <?php $pct = $stats['total'] ? round($row['c'] / $stats['total'] * 100) : 0; ?>
                    <div
                        style="padding:10px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;font-size:.82rem;">
                        <span
                            style="min-width:80px;color:var(--white);"><?= htmlspecialchars($row['browser'] ?: 'Unknown') ?></span>
                        <div style="flex:1;background:var(--bg3);border-radius:4px;height:6px;overflow:hidden;">
                            <div style="width:<?= $pct ?>%;height:100%;background:#8B5CF6;border-radius:4px;"></div>
                        </div>
                        <span style="min-width:30px;text-align:right;color:var(--muted);"><?= $row['c'] ?></span>
                    </div>
              <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── VISITORS TABLE ─────────────────────────────────────────────────────── -->
<div class="card">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-table-list"></i> Visit Log</h2>
            <p>
                <?= count($visits) ?> record
                <?= count($visits) !== 1 ? 's' : '' ?>
                &nbsp;·&nbsp;
                <?= $rangeLabels[$range] ?>
                <?php if ($range === 'custom'): ?>
                    (
                    <?= htmlspecialchars($from) ?> →
                    <?= htmlspecialchars($to) ?>)
                <?php endif; ?>
            </p>
        </div>
    </div>
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <?php if (empty($visits)): ?>
            <div style="padding:40px;text-align:center;color:var(--muted);">
                <i class="fa-solid fa-ghost" style="font-size:2rem;margin-bottom:12px;display:block;"></i>
                No visits recorded for this period.
            </div>
        <?php else: ?>
            <table class="visitors-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Time</th>
                        <th>IP</th>
                        <th>Location</th>
                        <th>Page</th>
                        <th>Browser</th>
                        <th>OS</th>
                        <th>Device</th>
                        <th>Referrer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visits as $i => $v): ?>
                        <tr>
                            <td class="vt-num">
                                <?= count($visits) - $i ?>
                            </td>
                            <td class="vt-time" title="<?= htmlspecialchars($v['visited_at']) ?>">
                                <?= date('M j, H:i', strtotime($v['visited_at'])) ?>
                            </td>
                            <td class="vt-ip"><code><?= htmlspecialchars($v['ip']) ?></code></td>
                            <td class="vt-loc">
                                <?php if ($v['country_code']): ?>
                                    <span class="vt-flag" title="<?= htmlspecialchars($v['country']) ?>">
                                        <?= countryFlag($v['country_code']) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="vt-city">
                                    <?= htmlspecialchars(implode(', ', array_filter([$v['city'], $v['country']]))) ?: '—' ?>
                                </span>
                            </td>
                            <td>
                                <span class="vt-badge">
                                    <?= htmlspecialchars($v['page'] ?: '—') ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($v['browser'] ?: '—') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($v['os'] ?: '—') ?>
                            </td>
                            <td>
                                <?php
                                $icon = match ($v['device']) {
                                    'Mobile' => 'fa-mobile-screen-button',
                                    'Tablet' => 'fa-tablet-screen-button',
                                    default => 'fa-desktop',
                                };
                                ?>
                                <i class="fa-solid <?= $icon ?>" title="<?= htmlspecialchars($v['device']) ?>"></i>
                                <?= htmlspecialchars($v['device'] ?: '—') ?>
                            </td>
                            <td class="vt-ref" title="<?= htmlspecialchars($v['referrer']) ?>">
                                <?php if ($v['referrer']): ?>
                                    <span>
                                        <?= htmlspecialchars(parse_url($v['referrer'], PHP_URL_HOST) ?: $v['referrer']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--muted);">Direct</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
    /* ── Filter bar ────────────────────────────────────────────────────────────── */
    .visitors-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .vf-btn {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        font-family: inherit;
        background: var(--bg2);
        color: var(--text);
        border: 1px solid var(--border);
        cursor: pointer;
        text-decoration: none;
        transition: background .15s, border-color .15s, color .15s;
        white-space: nowrap;
    }

    .vf-btn:hover {
        background: var(--bg3);
        border-color: var(--blue);
        color: var(--white);
    }

    .vf-btn.active {
        background: var(--blue);
        border-color: var(--blue);
        color: #fff;
    }

    .vf-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: 4px;
    }

    .vf-date {
        padding: 6px 10px;
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-size: .82rem;
        font-family: inherit;
        cursor: pointer;
    }

    .vf-date.active-input {
        border-color: var(--blue);
    }

    .vf-sep {
        color: var(--muted);
        font-size: .78rem;
    }

    /* ── Visitors table ────────────────────────────────────────────────────────── */
    .visitors-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }

    .visitors-table thead tr {
        border-bottom: 1px solid var(--border);
    }

    .visitors-table th {
        padding: 10px 16px;
        text-align: left;
        color: var(--muted);
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 600;
        white-space: nowrap;
    }

    .visitors-table td {
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
        white-space: nowrap;
    }

    .visitors-table tbody tr:last-child td {
        border-bottom: none;
    }

    .visitors-table tbody tr:hover td {
        background: var(--bg3);
    }

    .vt-num {
        color: var(--muted);
        font-size: .72rem;
        font-weight: 600;
    }

    .vt-time {
        color: var(--muted);
        font-size: .78rem;
    }

    .vt-ip code {
        background: var(--bg3);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: .76rem;
        color: var(--text);
    }

    .vt-loc {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .vt-flag {
        font-size: 1.1rem;
        line-height: 1;
    }

    .vt-city {
        white-space: nowrap;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .vt-badge {
        display: inline-block;
        padding: 2px 8px;
        background: color-mix(in srgb, var(--blue) 15%, transparent);
        color: var(--blue);
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .vt-ref {
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--muted);
        font-size: .78rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?= json_encode($vtLabels) ?>;
    const data   = <?= json_encode($vtCnts) ?>;
    const max    = <?= (int) $vtMax ?>;
    new Chart(document.getElementById('vtChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Visits',
                data,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59,130,246,.1)',
                borderWidth: 2,
                pointRadius: data.map(v => v > 0 ? 4 : 0),
                pointBackgroundColor: '#3B82F6',
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6B7280', font: { size: 11 }, maxTicksLimit: 12 } },
                y: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6B7280', stepSize: 1, font: { size: 11 } }, min: 0, max: max + 1 }
            }
        }
    });
})();
</script>