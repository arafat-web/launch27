<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

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

$sc = $statusColors[$booking['status']] ?? '#6B7280';
$updated = $_GET['updated'] ?? '';
$pricing = $booking['pricing_params'] ? json_decode($booking['pricing_params'], true) : [];
$dt = $booking['service_date'] ? strtotime($booking['service_date']) : null;
?>

<div class="page-hdr" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <a href="<?= $base ?>/admin/bookings"
            style="color:var(--muted);font-size:.82rem;text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Bookings
        </a>
        <h1 style="margin:0;"><i class="fa-solid fa-calendar-check"
                style="color:var(--blue);margin-right:8px;"></i>Booking #
            <?= $booking['id'] ?>
        </h1>
        <?php if ($booking['l27_id']): ?>
            <p style="margin:4px 0 0;font-size:.82rem;">
                Launch27 Ref: <code
                    style="background:var(--bg3);padding:2px 8px;border-radius:4px;"><?= htmlspecialchars($booking['l27_id']) ?></code>
            </p>
        <?php endif; ?>
    </div>
    <span class="bk-status"
        style="background:<?= $sc ?>1a;color:<?= $sc ?>;border:1px solid <?= $sc ?>44;font-size:.85rem;padding:6px 16px;">
        <span
            style="width:8px;height:8px;border-radius:50%;background:<?= $sc ?>;display:inline-block;margin-right:7px;flex-shrink:0;"></span>
        <?= $statusLabels[$booking['status']] ?? ucfirst($booking['status']) ?>
    </span>
</div>

<?php if ($updated): ?>
    <div class="alert alert-success" style="margin:16px 0;">
        <i class="fa-solid fa-circle-check"></i> Status updated successfully.
    </div>
<?php endif; ?>

<div class="bk-detail-grid">

    <!-- ── CUSTOMER DETAILS ─────────────────────────────────────────────── -->
    <div class="card bk-detail-card">
        <div class="card-head">
            <h2><i class="fa-solid fa-user"></i> Customer</h2>
        </div>
        <div class="card-body bk-kv">
            <div class="bk-kv-row"><span>Name</span><strong>
                    <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
                </strong></div>
            <div class="bk-kv-row"><span>Email</span><a href="mailto:<?= htmlspecialchars($booking['email']) ?>">
                    <?= htmlspecialchars($booking['email']) ?>
                </a></div>
            <div class="bk-kv-row"><span>Phone</span>
                <?= htmlspecialchars($booking['phone'] ?: '—') ?>
            </div>
        </div>
    </div>

    <!-- ── ADDRESS ─────────────────────────────────────────────────────── -->
    <div class="card bk-detail-card">
        <div class="card-head">
            <h2><i class="fa-solid fa-location-dot"></i> Address</h2>
        </div>
        <div class="card-body bk-kv">
            <div class="bk-kv-row"><span>Street</span>
                <?= htmlspecialchars($booking['address'] ?: '—') ?>
            </div>
            <div class="bk-kv-row"><span>City</span>
                <?= htmlspecialchars($booking['city'] ?: '—') ?>
            </div>
            <div class="bk-kv-row"><span>State</span>
                <?= htmlspecialchars($booking['state'] ?: '—') ?>
            </div>
            <div class="bk-kv-row"><span>ZIP</span>
                <?= htmlspecialchars($booking['zip'] ?: '—') ?>
            </div>
        </div>
    </div>

    <!-- ── SERVICE DETAILS ─────────────────────────────────────────────── -->
    <div class="card bk-detail-card">
        <div class="card-head">
            <h2><i class="fa-solid fa-broom"></i> Service</h2>
        </div>
        <div class="card-body bk-kv">
            <div class="bk-kv-row"><span>Service</span><strong>
                    <?= htmlspecialchars($booking['service_name'] ?: 'Service #' . $booking['service_id']) ?>
                </strong></div>
            <div class="bk-kv-row"><span>Frequency</span>
                <?= htmlspecialchars(ucfirst($booking['frequency'] ?: '—')) ?>
            </div>
            <div class="bk-kv-row"><span>Date</span>
                <?= $dt ? date('l, F j, Y', $dt) : '—' ?>
            </div>
            <div class="bk-kv-row"><span>Time</span>
                <?= $dt ? date('g:i A', $dt) : '—' ?>
            </div>
            <div class="bk-kv-row"><span>Arrival Window</span>
                <?= $booking['arrival_window'] ? $booking['arrival_window'] . ' hrs' : '—' ?>
            </div>
            <?php if ($booking['notes']): ?>
                <div class="bk-kv-row bk-kv-full"><span>Notes</span><em>
                        <?= nl2br(htmlspecialchars($booking['notes'])) ?>
                    </em></div>
            <?php endif; ?>

            <?php if (is_array($pricing) && count($pricing)): ?>
                <?php foreach ($pricing as $k => $v): ?>
                    <div class="bk-kv-row" style="margin-top:4px;">
                        <span>
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $k))) ?>
                        </span>
                        <?= is_numeric($v) ? number_format((float) $v, 0) : htmlspecialchars((string) $v) ?>
                    </div>
                <?php endforeach; ?>
                <div class="bk-total-row">
                    <span>Total Amount</span>
                    <strong>$<?= number_format($booking['total'], 2) ?></strong>
                </div>
            <?php else: ?>
                <div class="bk-total-row">
                    <span>Total Amount</span>
                    <strong>$<?= number_format($booking['total'], 2) ?></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── STATUS MANAGEMENT ───────────────────────────────────────────── -->
    <div class="card bk-detail-card" style="grid-column:1/-1;">
        <div class="card-head">
            <h2><i class="fa-solid fa-pen-to-square"></i> Update Status</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= $base ?>/admin/bookings/status"
                style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
                <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                <input type="hidden" name="from_detail" value="detail">
                <div class="fg" style="flex:1;min-width:160px;margin:0;">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach ($statusLabels as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= $booking['status'] === $val ? ' selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fg" style="flex:2;min-width:200px;margin:0;">
                    <label>Internal Note <span class="hint">Optional</span></label>
                    <input type="text" name="note" value="<?= htmlspecialchars($booking['status_note']) ?>"
                        placeholder="e.g. Confirmed via phone call">
                </div>
                <button type="submit" class="btn btn-primary" style="flex-shrink:0;">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>
            </form>
            <?php if ($booking['status_note']): ?>
                <p style="margin:12px 0 0;font-size:.82rem;color:var(--muted);">
                    <i class="fa-solid fa-note-sticky" style="margin-right:4px;"></i>
                    Current note: <em>
                        <?= htmlspecialchars($booking['status_note']) ?>
                    </em>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── META ────────────────────────────────────────────────────────── -->
    <div class="card bk-detail-card" style="grid-column:1/-1;">
        <div class="card-head">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Timeline</h2>
        </div>
        <div class="card-body bk-kv">
            <div class="bk-kv-row"><span>Booked At</span>
                <?= date('F j, Y \a\t g:i A', strtotime($booking['created_at'])) ?>
            </div>
            <div class="bk-kv-row"><span>Last Updated</span>
                <?= date('F j, Y \a\t g:i A', strtotime($booking['updated_at'])) ?>
            </div>
            <?php if ($booking['raw_response']): ?>
                <div class="bk-kv-row bk-kv-full" style="margin-top:8px;">
                    <span>L27 Raw Response</span>
                    <details>
                        <summary style="cursor:pointer;color:var(--muted);font-size:.78rem;">Show / Hide</summary>
                        <pre
                            style="margin-top:8px;padding:12px;background:var(--bg3);border-radius:8px;font-size:.72rem;overflow-x:auto;white-space:pre-wrap;word-break:break-all;max-height:200px;overflow-y:auto;"><?= htmlspecialchars($booking['raw_response']) ?></pre>
                    </details>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- end grid -->

<style>
    .bk-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-top: 20px;
    }

    @media (min-width: 850px) {
        .bk-detail-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .bk-detail-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .bk-detail-card .card-head h2 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: .95rem;
        color: var(--white);
    }

    .bk-detail-card .card-head h2 i {
        color: var(--blue);
        background: rgba(37, 99, 235, 0.15);
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: .85rem;
        box-shadow: 0 0 12px rgba(37, 99, 235, 0.2);
    }

    .bk-kv {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .bk-kv-row {
        display: flex;
        align-items: baseline;
        gap: 10px;
        font-size: .85rem;
        transition: transform 0.25s ease, background 0.25s ease;
        padding: 4px 6px;
        border-radius: 6px;
        margin-left: -6px;
    }

    .bk-kv-row:hover {
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.03);
    }

    .bk-kv-row span {
        min-width: 110px;
        flex-shrink: 0;
        color: var(--muted);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .bk-kv-row a {
        color: var(--blue);
        text-decoration: none;
        font-weight: 500;
    }

    .bk-kv-row a:hover {
        text-decoration: underline;
    }

    .bk-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding: 14px 20px;
        background: rgba(37, 99, 235, 0.08);
        border: 1px solid rgba(37, 99, 235, 0.2);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.1);
    }

    .bk-total-row span {
        color: var(--muted);
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
    }

    .bk-total-row strong {
        color: var(--white);
        font-size: 1.3rem;
        font-weight: 800;
    }

    .bk-kv-full {
        flex-direction: column;
    }

    .bk-kv-full span {
        min-width: auto;
        margin-bottom: 4px;
    }

    .bk-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.05);
    }

    pre::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    pre::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }
</style>