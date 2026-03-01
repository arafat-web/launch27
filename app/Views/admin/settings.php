<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$messages = [
    'ok'       => ['success', 'Password changed successfully.'],
    'wrong'    => ['error',   'Current password is incorrect.'],
    'mismatch' => ['error',   'New passwords do not match.'],
    'short'    => ['error',   'New password must be at least 6 characters.'],
];
$msgData = $msg ? ($messages[$msg] ?? null) : null;
?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-gear" style="color:var(--blue);margin-right:8px;"></i>Settings</h1>
    <p>Manage your admin account credentials.</p>
</div>

<?php if ($msgData): ?>
<div class="alert alert-<?= $msgData[0] ?>">
    <i class="fa-solid fa-<?= $msgData[0] === 'success' ? 'circle-check' : 'circle-exclamation' ?>"></i>
    <?= htmlspecialchars($msgData[1]) ?>
</div>
<?php endif; ?>

<div class="card" style="max-width:480px;">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-key"></i> Change Password</h2>
            <p>Update your admin login credentials.</p>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= $base ?>/admin/settings">
            <div class="fg">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <div class="form-divider"></div>
            <div class="fg">
                <label>New Password <span class="hint">Min. 6 characters</span></label>
                <input type="password" name="new_password" placeholder="••••••••" required autocomplete="new-password">
            </div>
            <div class="fg">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required autocomplete="new-password">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Password</button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="max-width:480px;margin-top:0;">
    <div class="card-head">
        <div>
            <h2><i class="fa-solid fa-circle-info"></i> Account Info</h2>
        </div>
    </div>
    <div class="card-body">
        <div style="display:flex;gap:12px;align-items:center;">
            <div class="nav-avatar" style="width:44px;height:44px;font-size:1rem;">
                <?= strtoupper(substr($_user['username'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight:700;color:var(--white);"><?= htmlspecialchars($_user['username'] ?? '') ?></div>
                <div style="font-size:.78rem;color:var(--muted);">Administrator</div>
            </div>
        </div>
    </div>
</div>
