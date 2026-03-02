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
        if (Auth::check()) {
            Auth::redirect('/admin');
        }
        $error = '';
        $this->renderAdmin('admin/login', compact('error'), 'Login — BronxHomeServices Admin');
    }

    public function doLogin(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = Database::verifyLogin($username, $password);

        if ($user) {
            Auth::login($user);
            Auth::redirect('/admin');
        }

        $error = 'Invalid username or password.';
        $this->renderAdmin('admin/login', compact('error'), 'Login — BronxHomeServices Admin');
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
        $seoRows = Database::getAllSeo();
        $contentRows = Database::getContent();
        $stats = Database::getDashboardStats();
        $this->renderAdmin('admin/dashboard', array_merge(compact('seoRows', 'contentRows'), $stats));
    }

    // ── SEO ───────────────────────────────────────────────────────────────────
    public function seo(): void
    {
        Auth::guard();
        $pages = ['home', 'booking'];
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
                trim($_POST[$prefix . 'title'] ?? ''),
                trim($_POST[$prefix . 'description'] ?? ''),
                trim($_POST[$prefix . 'keywords'] ?? ''),
                trim($_POST[$prefix . 'og_image'] ?? '')
            );
        }
        Auth::redirect('/admin/seo?saved=1');
    }

    // ── CONTENT ───────────────────────────────────────────────────────────────
    public function content(): void
    {
        Auth::guard();
        $content = Database::getContent();
        $saved = $_GET['saved'] ?? false;
        $this->renderAdmin('admin/content', compact('content', 'saved'));
    }

    public function saveContent(): void
    {
        Auth::guard();
        $allowed = array_keys(Database::getContent());
        $data = [];
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
        $current = trim($_POST['current_password'] ?? '');
        $new = trim($_POST['new_password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

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

    // ── VISITORS ──────────────────────────────────────────────────────────────
    public function visitors(): void
    {
        Auth::guard();

        $range = $_GET['range'] ?? 'today';
        $today = date('Y-m-d');

        switch ($range) {
            case 'week':
                $from = date('Y-m-d', strtotime('monday this week'));
                $to = $today;
                break;
            case 'month':
                $from = date('Y-m-01');
                $to = $today;
                break;
            case 'year':
                $from = date('Y-01-01');
                $to = $today;
                break;
            case 'custom':
                $from = $_GET['from'] ?? $today;
                $to = $_GET['to'] ?? $today;
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from))
                    $from = $today;
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))
                    $to = $today;
                if ($from > $to)
                    [$from, $to] = [$to, $from];
                break;
            default:
                $range = 'today';
                $from = $today;
                $to = $today;
        }

        $visits = Database::getVisits($from, $to);
        $stats = Database::getVisitStats($from, $to);

        $this->renderAdmin('admin/visitors', compact('visits', 'stats', 'range', 'from', 'to'), 'Visitors — BronxHomeServices Admin');
    }

    // ── BOOKINGS ──────────────────────────────────────────────────────────────
    public function bookings(): void
    {
        Auth::guard();
        $filterStatus = $_GET['status'] ?? 'all';
        $bookings = Database::getBookings();          // all, for stat cards
        $displayed = ($filterStatus === 'all')
            ? $bookings
            : array_values(array_filter($bookings, fn($b) => $b['status'] === $filterStatus));

        $this->renderAdmin('admin/bookings', compact('bookings', 'displayed', 'filterStatus'), 'Bookings — BronxHomeServices Admin');
    }

    public function updateBookingStatus(): void
    {
        Auth::guard();
        $id = (int) ($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $back = trim($_POST['redirect_status'] ?? 'all');
        $from = trim($_POST['from_detail'] ?? '');

        $allowed = ['pending', 'confirmed', 'in-progress', 'completed', 'cancelled'];
        if ($id > 0 && in_array($status, $allowed)) {
            Database::updateBookingStatus($id, $status, $note);
        }

        // If came from detail page, return there
        if ($from === 'detail' && $id > 0) {
            Auth::redirect('/admin/bookings/' . $id . '?updated=1');
        }

        Auth::redirect('/admin/bookings?status=' . urlencode($back) . '&updated=1');
    }

    // ── BOOKING DETAIL ────────────────────────────────────────────────────────
    public function bookingDetail(): void
    {
        Auth::guard();
        $id = (int) ($_GET['id'] ?? 0);
        $booking = $id ? Database::getBookingById($id) : null;

        if (!$booking) {
            http_response_code(404);
            $this->renderAdmin('admin/404', [], '404 — Not Found');
            return;
        }

        $this->renderAdmin('admin/booking_detail', compact('booking'), 'Booking #' . $id . ' — BronxHomeServices Admin');
    }

    // ── EXPORT BOOKINGS (CSV) ─────────────────────────────────────────────────
    public function exportBookings(): void
    {
        Auth::guard();
        $bookings = Database::getBookings();
        $filename = 'bookings_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache');
        $fp = fopen('php://output', 'w');

        fputcsv($fp, ['ID', 'L27 Ref', 'Date Booked', 'First Name', 'Last Name', 'Email', 'Phone', 'Address', 'City', 'State', 'ZIP', 'Service', 'Frequency', 'Appt Date', 'Total', 'Status', 'Note']);
        foreach ($bookings as $b) {
            fputcsv($fp, [
                $b['id'],
                $b['l27_id'],
                $b['created_at'],
                $b['first_name'],
                $b['last_name'],
                $b['email'],
                $b['phone'],
                $b['address'],
                $b['city'],
                $b['state'],
                $b['zip'],
                $b['service_name'],
                $b['frequency'],
                $b['service_date'],
                $b['total'],
                $b['status'],
                $b['status_note'],
            ]);
        }
        fclose($fp);
        exit;
    }

    // ── API LOGS VIEWER ───────────────────────────────────────────────────────
    public function apiLogs(): void
    {
        Auth::guard();
        $logDir = ROOT_DIR . '/logs';
        $logFiles = [];
        if (is_dir($logDir)) {
            foreach (array_reverse(glob($logDir . '/*.log')) as $f) {
                $logFiles[] = ['name' => basename($f), 'path' => $f];
            }
        }

        $selectedFile = isset($_GET['file']) ? basename($_GET['file']) : ($logFiles[0]['name'] ?? '');
        $entries = [];
        if ($selectedFile) {
            $fullPath = $logDir . '/' . $selectedFile;
            if (file_exists($fullPath) && realpath($fullPath) === realpath($fullPath)) {
                $lines = file($fullPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $lines = array_reverse(array_slice($lines, -200)); // latest 200
                foreach ($lines as $line) {
                    $decoded = json_decode($line, true);
                    $entries[] = $decoded ?: ['raw' => $line];
                }
            }
        }

        $this->renderAdmin('admin/logs', compact('logFiles', 'selectedFile', 'entries'), 'API Logs — BronxHomeServices Admin');
    }

    // ── RENDER HELPER ─────────────────────────────────────────────────────────
    private function renderAdmin(string $template, array $data = [], string $title = 'Admin — BronxHomeServices'): void
    {
        $data['_pageTitle'] = $title;
        $data['_user'] = Auth::user();
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
