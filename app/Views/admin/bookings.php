<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$statusList = ['pending', 'confirmed', 'in-progress', 'completed', 'cancelled'];
$statusLabels = [
    'pending' => 'Pending',
    'confirmed' => 'Confirmed',
    'in-progress' => 'In Progress',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];
$statusColors = [
    'pending' => '#F59E0B',
    'confirmed' => '#3B82F6',
    'in-progress' => '#8B5CF6',
    'completed' => '#10B981',
    'cancelled' => '#EF4444',
];

$filterStatus = $_GET['status'] ?? 'all';
$successMsg = $_GET['updated'] ?? '';

// Per-status counts (always computed from full $bookings list)
$counts = ['all' => count($bookings)];
foreach ($statusList as $s) {
    $counts[$s] = count(array_filter($bookings, fn($b) => $b['status'] === $s));
}
$total = count($bookings);
$revenue = array_sum(array_column($bookings, 'total'));
$pending = $counts['pending'];
$done = $counts['completed'];
?>

<div class="page-hdr"
    style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <h1><i class="fa-solid fa-calendar-check" style="color:var(--blue);margin-right:8px;"></i>Bookings</h1>
        <p>All bookings submitted through your website, mirrored from Launch27.</p>
    </div>
    <a href="<?= $base ?>/admin/bookings/export" class="btn btn-ghost"
        style="margin-top:4px;display:inline-flex;align-items:center;gap:7px;">
        <i class="fa-solid fa-file-csv"></i> Export CSV
    </a>
</div>

<?php if ($successMsg): ?>
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> Booking status updated successfully.
    </div>
<?php endif; ?>

<!-- ── STAT CARDS ─────────────────────────────────────────────────────────── -->
<div class="stats-row" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-list-check"></i></div>
        <div class="stat-val"><?= $total ?></div>
        <div class="stat-lbl">Total Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#F59E0B;"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-val"><?= $pending ?></div>
        <div class="stat-lbl">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#10B981;"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-val"><?= $done ?></div>
        <div class="stat-lbl">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#10B981;"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="stat-val">$<?= number_format($revenue, 0) ?></div>
        <div class="stat-lbl">Total Revenue</div>
    </div>
</div>

<!-- ── STATUS TAB STRIP ───────────────────────────────────────────────────── -->
<div class="bk-tab-strip">
    <a href="<?= $base ?>/admin/bookings?status=all"
        class="bk-tab<?= $filterStatus === 'all' ? ' bk-tab-active' : '' ?>">
        <span class="bk-tab-dot" style="background:#6B7280;"></span>
        All
        <span class="bk-tab-count"><?= $counts['all'] ?></span>
    </a>
    <?php foreach ($statusList as $s): ?>
        <a href="<?= $base ?>/admin/bookings?status=<?= $s ?>"
            class="bk-tab<?= $filterStatus === $s ? ' bk-tab-active' : '' ?>" <?= $filterStatus === $s ? 'style="--tab-color:' . $statusColors[$s] . ';"' : '' ?>>
            <span class="bk-tab-dot" style="background:<?= $statusColors[$s] ?>;"></span>
            <?= $statusLabels[$s] ?>
            <span class="bk-tab-count"><?= $counts[$s] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- ── BOOKINGS TABLE ─────────────────────────────────────────────────────── -->
<div class="card" style="border-radius:0 0 14px 14px;border-top:none;margin-top:0;">
    <div class="card-body" style="padding:0;overflow-x:auto;">
        <?php if (empty($displayed)): ?>
            <div style="padding:52px;text-align:center;color:var(--muted);">
                <i class="fa-solid fa-calendar-xmark"
                    style="font-size:2.5rem;margin-bottom:14px;display:block;opacity:.35;"></i>
                <p style="margin:0;font-size:.9rem;">No
                    bookings<?= $filterStatus !== 'all' ? ' with status <strong>' . htmlspecialchars($statusLabels[$filterStatus] ?? $filterStatus) . '</strong>' : '' ?>.
                </p>
            </div>
        <?php else: ?>
            <table class="visitors-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booked</th>
                        <th>L27 Ref</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Appt. Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($displayed as $b): ?>
                        <?php $sc = $statusColors[$b['status']] ?? '#6B7280'; ?>
                        <tr style="cursor:pointer;" onclick="window.location='<?= $base ?>/admin/bookings/<?= $b['id'] ?>'">
                            <td class="vt-num">
                                <a href="<?= $base ?>/admin/bookings/<?= $b['id'] ?>"
                                    style="color:var(--blue);font-weight:700;text-decoration:none;">#<?= $b['id'] ?></a>
                            </td>
                            <td class="vt-time">
                                <?= date('M j, Y', strtotime($b['created_at'])) ?><br>
                                <span style="font-size:.7rem;"><?= date('H:i', strtotime($b['created_at'])) ?></span>
                            </td>
                            <td>
                                <code style="background:var(--bg3);padding:2px 7px;border-radius:4px;font-size:.74rem;">
                                            <?= htmlspecialchars($b['l27_id'] ?: '—') ?>
                                        </code>
                            </td>
                            <td>
                                <div style="font-weight:600;color:var(--white);">
                                    <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?>
                                </div>
                                <div style="font-size:.74rem;color:var(--muted);"><?= htmlspecialchars($b['email']) ?></div>
                                <?php if ($b['phone']): ?>
                                    <div style="font-size:.74rem;color:var(--muted);"><?= htmlspecialchars($b['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:500;"><?= htmlspecialchars($b['service_name'] ?: '—') ?></div>
                                <div style="font-size:.74rem;color:var(--muted);text-transform:capitalize;">
                                    <?= htmlspecialchars($b['frequency'] ?: '—') ?>
                                </div>
                            </td>
                            <td style="white-space:nowrap;"><?php
                            $dt = $b['service_date'] ? strtotime($b['service_date']) : null;
                            echo $dt
                                ? date('M j, Y', $dt) . '<br><span style="font-size:.72rem;color:var(--muted);">' . date('g:i A', $dt) . '</span>'
                                : '—';
                            ?></td>
                            <td style="font-weight:700;"><?= $b['total'] ? '$' . number_format($b['total'], 2) : '—' ?></td>
                            <td>
                                <span class="bk-status"
                                    style="background:<?= $sc ?>1a;color:<?= $sc ?>;border:1px solid <?= $sc ?>44;">
                                    <span
                                        style="width:6px;height:6px;border-radius:50%;background:<?= $sc ?>;display:inline-block;margin-right:5px;flex-shrink:0;"></span>
                                    <?= $statusLabels[$b['status']] ?? ucfirst($b['status']) ?>
                                </span>
                                <?php if ($b['status_note']): ?>
                                    <div style="font-size:.7rem;color:var(--muted);margin-top:3px;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                        title="<?= htmlspecialchars($b['status_note']) ?>">
                                        <i class="fa-solid fa-note-sticky"
                                            style="margin-right:2px;"></i><?= htmlspecialchars($b['status_note']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="bk-action-btn"
                                    onclick="openModal(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['first_name'] . ' ' . $b['last_name'])) ?>', '<?= $b['status'] ?>', '<?= htmlspecialchars(addslashes($b['status_note'])) ?>')">
                                    <i class="fa-solid fa-pen-to-square"></i> Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- ── STATUS UPDATE MODAL ────────────────────────────────────────────────── -->
<div id="bkModal" class="bk-modal-overlay" style="display:none;" onclick="if(event.target===this)closeModal()">
    <div class="bk-modal">
        <div class="bk-modal-head">
            <h3><i class="fa-solid fa-pen-to-square" style="color:var(--blue);margin-right:8px;"></i>Update Status</h3>
            <button onclick="closeModal()"
                style="background:none;border:none;color:var(--muted);font-size:1.2rem;cursor:pointer;line-height:1;padding:0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p id="modalName" style="margin:0 0 18px;color:var(--muted);font-size:.85rem;"></p>
        <form method="POST" action="<?= $base ?>/admin/bookings/status">
            <input type="hidden" name="id" id="modalId">
            <input type="hidden" name="redirect_status" value="<?= htmlspecialchars($filterStatus) ?>">
            <div class="fg">
                <label>New Status</label>
                <select name="status" id="modalStatus">
                    <?php foreach ($statusList as $s): ?>
                        <option value="<?= $s ?>"><?= $statusLabels[$s] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fg" style="margin-bottom:22px;">
                <label>Internal Note <span class="hint">Optional — admin only</span></label>
                <textarea name="note" id="modalNote" placeholder="e.g. Customer called to reschedule…"
                    style="height:80px;"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeModal()" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save
                    Status</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ── Bookings Table ─────────────────────────────────────────────────────── */
    .visitors-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .visitors-table th {
        background: var(--bg2);
        color: var(--muted);
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .visitors-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: .85rem;
        vertical-align: middle;
    }

    .visitors-table tbody tr {
        transition: background .15s;
    }

    .visitors-table tbody tr:hover {
        background: var(--bg2);
    }

    .vt-num {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color: var(--muted);
    }

    .vt-time {
        white-space: nowrap;
        color: var(--muted);
    }

    /* ── Tab strip ─────────────────────────────────────────────────────────── */
    .bk-tab-strip {
        display: flex;
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 14px 14px 0 0;
        border-bottom: none;
        padding: 6px 6px 0;
        overflow-x: auto;
        flex-wrap: nowrap;
        gap: 2px;
    }

    .bk-tab {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px 11px;
        border-radius: 10px 10px 0 0;
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
        transition: color .15s, background .15s;
        position: relative;
        bottom: -1px;
    }

    .bk-tab:hover {
        color: var(--text);
        background: var(--bg3);
    }

    .bk-tab-active {
        color: var(--white);
        background: var(--bg3);
        border-bottom: 2px solid var(--tab-color, var(--blue));
    }

    .bk-tab-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .bk-tab-count {
        background: var(--bg1);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 1px 8px;
        font-size: .7rem;
        font-weight: 700;
        color: var(--muted);
        min-width: 22px;
        text-align: center;
        line-height: 1.6;
    }

    .bk-tab-active .bk-tab-count {
        background: color-mix(in srgb, var(--tab-color, var(--blue)) 14%, transparent);
        border-color: color-mix(in srgb, var(--tab-color, var(--blue)) 35%, transparent);
        color: var(--tab-color, var(--blue));
    }

    /* ── Status badge ───────────────────────────────────────────────────────── */
    .bk-status {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ── Update button ──────────────────────────────────────────────────────── */
    .bk-action-btn {
        padding: 5px 12px;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 7px;
        color: var(--blue);
        font-size: .78rem;
        font-family: inherit;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s, border-color .15s;
    }

    .bk-action-btn:hover {
        background: color-mix(in srgb, var(--blue) 12%, transparent);
        border-color: var(--blue);
    }

    /* ── Modal ──────────────────────────────────────────────────────────────── */
    .bk-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bk-modal {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 24px 64px rgba(0, 0, 0, .5);
        animation: modalIn .18s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(.96) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .bk-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .bk-modal-head h3 {
        margin: 0;
        font-size: 1rem;
    }
</style>

<script>
    function openModal(id, name, status, note) {
        document.getElementById('bkModal').style.display = 'flex';
        document.getElementById('modalId').value = id;
        document.getElementById('modalName').textContent = 'Customer: ' + name;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalNote').value = note;
    }
    function closeModal() {
        document.getElementById('bkModal').style.display = 'none';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>