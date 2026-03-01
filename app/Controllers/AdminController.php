<?php
/**
 * AdminController
 * ───────────────
 * Handles all /admin routes: login, dashboard, SEO, content, settings.
 */
class AdminController
{
    // ── AUTH ──────────────────────────────────────────────────────────────────
    public function login(): void
    {
        if (Auth::check()) { Auth::redirect('/admin'); }
        $error = '';
        $this->renderAdmin('admin/login', compact('error'), 'Login — Clean27 Admin');
    }

    public function doLogin(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user     = Database::verifyLogin($username, $password);

        if ($user) {
            Auth::login($user);
            Auth::redirect('/admin');
        }

        $error = 'Invalid username or password.';
        $this->renderAdmin('admin/login', compact('error'), 'Login — Clean27 Admin');
    }

    public function logout(): void
    {
        Auth::logout();
        Auth::redirect('/admin/login');
    }

    // ── DASHBOARD ─────────────────────────────────────────────────────────────
    public function dashboard(): void
    {
        Auth::guard();
        $seoRows     = Database::getAllSeo();
        $contentRows = Database::getContent();
        $this->renderAdmin('admin/dashboard', compact('seoRows', 'contentRows'));
    }

    // ── SEO ───────────────────────────────────────────────────────────────────
    public function seo(): void
    {
        Auth::guard();
        $pages   = ['home', 'booking'];
        $seoData = [];
        foreach ($pages as $p) {
            $seoData[$p] = Database::getSeo($p);
        }
        $saved = $_GET['saved'] ?? false;
        $this->renderAdmin('admin/seo', compact('seoData', 'saved'));
    }

    public function saveSeo(): void
    {
        Auth::guard();
        $pages = ['home', 'booking'];
        foreach ($pages as $page) {
            $prefix = $page . '_';
            Database::saveSeo(
                $page,
                trim($_POST[$prefix . 'title']       ?? ''),
                trim($_POST[$prefix . 'description']  ?? ''),
                trim($_POST[$prefix . 'keywords']     ?? ''),
                trim($_POST[$prefix . 'og_image']     ?? '')
            );
        }
        Auth::redirect('/admin/seo?saved=1');
    }

    // ── CONTENT ───────────────────────────────────────────────────────────────
    public function content(): void
    {
        Auth::guard();
        $content = Database::getContent();
        $saved   = $_GET['saved'] ?? false;
        $this->renderAdmin('admin/content', compact('content', 'saved'));
    }

    public function saveContent(): void
    {
        Auth::guard();
        $allowed = array_keys(Database::getContent());
        $data    = [];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = $_POST[$key];
            }
        }
        Database::saveContent($data);
        Auth::redirect('/admin/content?saved=1');
    }

    // ── SETTINGS ─────────────────────────────────────────────────────────────
    public function settings(): void
    {
        Auth::guard();
        $msg = $_GET['msg'] ?? '';
        $this->renderAdmin('admin/settings', compact('msg'));
    }

    public function saveSettings(): void
    {
        Auth::guard();
        $current  = trim($_POST['current_password'] ?? '');
        $new      = trim($_POST['new_password']     ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');

        $user = Database::verifyLogin(Auth::user()['username'], $current);
        if (!$user) {
            Auth::redirect('/admin/settings?msg=wrong');
        }
        if (strlen($new) < 6) {
            Auth::redirect('/admin/settings?msg=short');
        }
        if ($new !== $confirm) {
            Auth::redirect('/admin/settings?msg=mismatch');
        }
        Database::updatePassword(Auth::user()['id'], $new);
        Auth::redirect('/admin/settings?msg=ok');
    }

    // ── RENDER HELPER ─────────────────────────────────────────────────────────
    private function renderAdmin(string $template, array $data = [], string $title = 'Admin — Clean27'): void
    {
        $data['_pageTitle'] = $title;
        $data['_user']      = Auth::user();
        extract($data, EXTR_SKIP);

        // Capture view
        ob_start();
        $viewFile = VIEW_DIR . '/' . $template . '.php';
        include $viewFile;
        $content = ob_get_clean();

        // Render admin layout (different from public main.php)
        include VIEW_DIR . '/admin/layout.php';
    }
}
