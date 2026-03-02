<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$seoCount = count($seoRows ?? []);
$contentCount = count($contentRows ?? []);

$statusColors = [
    'pending' => '#F59E0B',
    'confirmed' => '#3B82F6',
    'in-progress' => '#8B5CF6',
    'completed' => '#10B981',
    'cancelled' => '#EF4444',
];
$statusLabels = [
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'in-progress' => 'In Progress',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];

// Build 30-day chart data
$chartDays = [];
$chartCnts = [];
$perDayMap = [];
foreach ($perDay ?? [] as $row) {
    $perDayMap[$row['day']] = (int) $row['cnt'];
}
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chartDays[] = date('M j', strtotime($d));
    $chartCnts[] = $perDayMap[$d] ?? 0;
}
?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-gauge" style="color:var(--blue);margin-right:8px;"></i>Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_user['username'] ?? 'Admin') ?>. Here's what's happening today.</p>
</div>

<!-- ── BOOKING STAT CARDS ────────────────────────────────────────────────── -->
<div class="stats-row" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="color:var(--blue);"><i class="fa-solid fa-calendar-check"></i></div>
        <div class="stat-val"><?= (int) ($total ?? 0) ?></div>
        <div class="stat-lbl">Total Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#F59E0B;"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-val"><?= (int) ($pending ?? 0) ?></div>
        <div class="stat-lbl">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#10B981;"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="stat-val">$<?= number_format((float) ($revenue ?? 0), 0) ?></div>
        <div class="stat-lbl">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#8B5CF6;"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="stat-val"><?= (int) ($todayCnt ?? 0) ?></div>
        <div class="stat-lbl">Booked Today</div>
    </div>
</div>

<div class="db-two-col">

    <!-- ── TODAY'S APPOINTMENTS ────────────────────────────────────────── -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-calendar-day" style="color:#F59E0B;"></i> Today's Appointments</h2>
                <p><?= date('l, F j, Y') ?></p>
            </div>
            <a href="<?= $base ?>/admin/bookings" class="btn btn-ghost" style="font-size:.78rem;padding:6px 12px;">View
                All</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($todayAppts)): ?>
                <div style="padding:32px;text-align:center;color:var(--muted);">
                    <i class="fa-solid fa-moon" style="font-size:1.8rem;display:block;margin-bottom:10px;opacity:.35;"></i>
                    <p style="margin:0;font-size:.85rem;">No appointments scheduled for today.</p>
                </div>
            <?php else: ?>
                <?php foreach ($todayAppts as $appt): ?>
                    <?php $sc = $statusColors[$appt['status']] ?? '#6B7280'; ?>
                    <div class="db-appt-row" onclick="window.location='<?= $base ?>/admin/bookings/<?= $appt['id'] ?>'">
                        <div class="db-appt-time">
                            <strong><?= $appt['service_date'] ? date('g:i A', strtotime($appt['service_date'])) : '—' ?></strong>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;color:var(--white);font-size:.88rem;">
                                <?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></div>
                            <div style="font-size:.75rem;color:var(--muted);">
                                <?= htmlspecialchars($appt['service_name'] ?: '—') ?></div>
                        </div>
                        <span
                            style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;background:<?= $sc ?>1a;color:<?= $sc ?>;border:1px solid <?= $sc ?>44;white-space:nowrap;">
                            <span style="width:5px;height:5px;border-radius:50%;background:<?= $sc ?>;flex-shrink:0;"></span>
                            <?= $statusLabels[$appt['status']] ?? ucfirst($appt['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── RECENT BOOKINGS ────────────────────────────────────────────── -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-list-check" style="color:var(--blue);"></i> Recent Bookings</h2>
                <p>Last 8 submitted</p>
            </div>
            <a href="<?= $base ?>/admin/bookings" class="btn btn-ghost" style="font-size:.78rem;padding:6px 12px;">View
                All</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($recent)): ?>
                <div style="padding:32px;text-align:center;color:var(--muted);">
                    <i class="fa-solid fa-calendar-xmark"
                        style="font-size:1.8rem;display:block;margin-bottom:10px;opacity:.35;"></i>
                    <p style="margin:0;font-size:.85rem;">No bookings yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recent as $b): ?>
                    <?php $sc = $statusColors[$b['status']] ?? '#6B7280'; ?>
                    <div class="db-appt-row" onclick="window.location='<?= $base ?>/admin/bookings/<?= $b['id'] ?>'">
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:600;color:var(--white);font-size:.88rem;">
                                <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></div>
                            <div style="font-size:.75rem;color:var(--muted);"><?= htmlspecialchars($b['service_name'] ?: '—') ?>
                                · <?= date('M j', strtotime($b['created_at'])) ?></div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-weight:700;font-size:.88rem;color:var(--white);">
                                <?= $b['total'] ? '$' . number_format($b['total'], 0) : '—' ?></div>
                            <span
                                style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:.68rem;font-weight:700;background:<?= $sc ?>1a;color:<?= $sc ?>;border:1px solid <?= $sc ?>44;">
                                <?= $statusLabels[$b['status']] ?? ucfirst($b['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- end two-col -->

<!-- ── 30-DAY BOOKINGS CHART ─────────────────────────────────────────────── -->
<div class="card" style="margin-top:20px;">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-chart-line" style="color:var(--blue);"></i> Bookings (last 30 days)</h2>
            <p>New bookings received each day</p>
        </div>
    </div>
    <div class="card-body">
        <canvas id="bkChart" style="width:100%;height:180px;"></canvas>
    </div>
</div>

<!-- ── QUICK ACTIONS ─────────────────────────────────────────────────────── -->
<div class="card" style="margin-top:20px;">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
        </div>
    </div>
    <div class="card-body">
        <div class="quick-grid">
            <a href="<?= $base ?>/admin/bookings" class="quick-card">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Bookings</span>
                <small>View &amp; manage all bookings</small>
            </a>
            <a href="<?= $base ?>/admin/visitors" class="quick-card">
                <i class="fa-solid fa-users"></i>
                <span>Visitors</span>
                <small>Traffic &amp; geolocation</small>
            </a>
            <a href="<?= $base ?>/admin/seo" class="quick-card">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
                <span>SEO</span>
                <small>Page titles &amp; meta</small>
            </a>
            <a href="<?= $base ?>/" target="_blank" class="quick-card">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>View Site</span>
                <small>Opens in a new tab</small>
            </a>
        </div>
    </div>
</div>

<style>
    .db-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 0;
    }

    @media (max-width: 760px) {
        .db-two-col {
            grid-template-columns: 1fr;
        }
    }

    .db-appt-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background .15s;
    }

    .db-appt-row:last-child {
        border-bottom: none;
    }

    .db-appt-row:hover {
        background: var(--bg3);
    }

    .db-appt-time {
        min-width: 62px;
        font-size: .78rem;
        color: var(--muted);
        flex-shrink: 0;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const labels = <?= json_encode($chartDays) ?>;
        const data = <?= json_encode($chartCnts) ?>;
        const max = Math.max(...data, 1);

        new Chart(document.getElementById('bkChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Bookings',
                    data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,.12)',
                    borderWidth: 2,
                    pointRadius: data.map(v => v > 0 ? 4 : 0),
                    pointBackgroundColor: '#3B82F6',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6B7280', font: { size: 11 }, maxTicksLimit: 10 } },
                    y: { grid: { color: 'rgba(255,255,255,.05)' }, ticks: { color: '#6B7280', stepSize: 1, font: { size: 11 } }, min: 0, max: max + 1 }
                }
            }
        });
    })();
</script>