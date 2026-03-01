<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-logo">Clean<span>27</span></div>
            <p class="login-tagline">Admin Panel — Sign in to continue</p>
        </div>
        <div class="login-box">
            <h2><i class="fa-solid fa-lock" style="color:var(--blue);margin-right:8px;"></i>Sign In</h2>

            <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') ?>/admin/login">
                <div class="fg">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="admin" autocomplete="username" required>
                </div>
                <div class="fg">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <p class="login-hint">Default credentials: <code>admin</code> / <code>admin123</code><br>Change your password in Settings after first login.</p>
        </div>
    </div>
</div>
