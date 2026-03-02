<?php
// This view is rendered through the main layout (layouts/main.php)
?>
<section class="confirm-page">
    <div class="confirm-card">

        <!-- Animated checkmark -->
        <div class="confirm-icon">
            <svg viewBox="0 0 52 52" class="confirm-check-svg">
                <circle class="confirm-circle" cx="26" cy="26" r="25" fill="none" />
                <path class="confirm-check" fill="none" d="M14 27l8 8 16-18" />
            </svg>
        </div>

        <h1>You're all set
            <?= $firstName ? ', ' . $firstName : '' ?>!
        </h1>
        <p class="confirm-sub">Your booking has been confirmed. We'll see you soon.</p>

        <!-- Booking summary -->
        <div class="confirm-summary">
            <div class="confirm-row">
                <span><i class="fa-solid fa-hashtag"></i> Booking ID</span>
                <strong>
                    <?= $bookingId ?>
                </strong>
            </div>
            <?php if ($serviceName): ?>
                <div class="confirm-row">
                    <span><i class="fa-solid fa-broom"></i> Service</span>
                    <strong>
                        <?= $serviceName ?>
                    </strong>
                </div>
            <?php endif; ?>
            <?php if ($date): ?>
                <div class="confirm-row">
                    <span><i class="fa-regular fa-calendar"></i> Date &amp; Time</span>
                    <strong>
                        <?= $date ?>
                    </strong>
                </div>
            <?php endif; ?>
            <?php if ($total): ?>
                <div class="confirm-row">
                    <span><i class="fa-solid fa-dollar-sign"></i> Total</span>
                    <strong>$
                        <?= number_format((float) $total, 2) ?>
                    </strong>
                </div>
            <?php endif; ?>
        </div>

        <p class="confirm-note">
            <i class="fa-solid fa-envelope" style="color:var(--primary);margin-right:6px;"></i>
            A confirmation has been sent to your email by Launch27.
        </p>

        <div class="confirm-actions">
            <a href="<?= View::url('/') ?>" class="btn-confirm-home">
                <i class="fa-solid fa-house"></i> Back to Home
            </a>
            <a href="<?= View::url('/booking') ?>" class="btn-confirm-book">
                <i class="fa-solid fa-plus"></i> Book Another
            </a>
        </div>

    </div>
</section>

<style>
    /* ── Page wrapper ────────────────────────────────────────────────────── */
    .confirm-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
    }

    .confirm-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 48px 40px;
        max-width: 500px;
        width: 100%;
        text-align: center;
        box-shadow: var(--shadow-lg);
        animation: cardIn .5s ease;
    }

    @keyframes cardIn {
        from {
            opacity: 0;
            transform: translateY(24px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Animated checkmark ─────────────────────────────────────────────── */
    .confirm-icon {
        margin-bottom: 24px;
    }

    .confirm-check-svg {
        width: 72px;
        height: 72px;
        display: block;
        margin: 0 auto;
    }

    .confirm-circle {
        stroke: #10B981;
        stroke-width: 2;
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-linecap: round;
        animation: drawCircle .8s cubic-bezier(.65, 0, .45, 1) forwards;
    }

    .confirm-check {
        stroke: #10B981;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: drawCheck .4s .8s cubic-bezier(.65, 0, .45, 1) forwards;
    }

    @keyframes drawCircle {
        to {
            stroke-dashoffset: 0;
        }
    }

    @keyframes drawCheck {
        to {
            stroke-dashoffset: 0;
        }
    }

    /* ── Text ───────────────────────────────────────────────────────────── */
    .confirm-card h1 {
        margin: 0 0 8px;
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--navy);
    }

    .confirm-sub {
        color: var(--muted);
        margin: 0 0 28px;
        font-size: .95rem;
    }

    /* ── Summary rows ───────────────────────────────────────────────────── */
    .confirm-summary {
        background: var(--silver-lt);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 6px 20px;
        margin-bottom: 22px;
        text-align: left;
    }

    .confirm-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 0;
        border-bottom: 1px solid var(--border);
        font-size: .88rem;
        gap: 12px;
    }

    .confirm-row:last-child {
        border-bottom: none;
    }

    .confirm-row span {
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .confirm-row strong {
        color: var(--navy);
        text-align: right;
    }

    /* ── Email note ─────────────────────────────────────────────────────── */
    .confirm-note {
        font-size: .8rem;
        color: var(--muted);
        margin: 0 0 28px;
    }

    /* ── Action buttons ─────────────────────────────────────────────────── */
    .confirm-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-confirm-home,
    .btn-confirm-book {
        padding: 11px 22px;
        border-radius: 10px;
        font-size: .88rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: opacity .2s, transform .15s;
    }

    .btn-confirm-home:hover,
    .btn-confirm-book:hover {
        opacity: .85;
        transform: translateY(-1px);
    }

    .btn-confirm-home {
        background: transparent;
        border: 1.5px solid var(--border);
        color: var(--muted);
    }

    .btn-confirm-home:hover {
        background: var(--silver-lt);
        color: var(--navy);
        border-color: var(--border);
    }

    .btn-confirm-book {
        background: var(--blue);
        border: 1px solid var(--blue);
        color: #fff;
    }

    .btn-confirm-book:hover {
        background: var(--blue-dark);
        border-color: var(--blue-dark);
        opacity: 1;
    }
</style>