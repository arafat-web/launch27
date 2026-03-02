<?php
/**
 * Database
 * ────────
 * PDO SQLite singleton. Creates and migrates the schema on first run.
 * Provides typed helpers for SEO settings and site content.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null)
            return self::$pdo;

        $dbPath = ROOT_DIR . '/database/launch27.db';
        if (!is_dir(ROOT_DIR . '/database')) {
            mkdir(ROOT_DIR . '/database', 0755, true);
        }

        self::$pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$pdo->exec('PRAGMA journal_mode=WAL;');
        self::migrate(self::$pdo);

        return self::$pdo;
    }

    // ── SCHEMA ────────────────────────────────────────────────────────────────
    private static function migrate(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                username      TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                created_at    TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS seo_settings (
                page        TEXT PRIMARY KEY,
                title       TEXT DEFAULT '',
                description TEXT DEFAULT '',
                keywords    TEXT DEFAULT '',
                og_image    TEXT DEFAULT '',
                updated_at  TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS site_content (
                key        TEXT PRIMARY KEY,
                value      TEXT DEFAULT '',
                label      TEXT DEFAULT '',
                type       TEXT DEFAULT 'text',
                updated_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS visitors (
                id           INTEGER PRIMARY KEY AUTOINCREMENT,
                ip           TEXT,
                country      TEXT,
                country_code TEXT,
                city         TEXT,
                region       TEXT,
                isp          TEXT,
                page         TEXT,
                referrer     TEXT,
                user_agent   TEXT,
                browser      TEXT,
                os           TEXT,
                device       TEXT,
                visited_at   TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS bookings (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                l27_id         TEXT,
                first_name     TEXT,
                last_name      TEXT,
                email          TEXT,
                phone          TEXT,
                address        TEXT,
                city           TEXT,
                state          TEXT,
                zip            TEXT,
                service_id     INTEGER,
                service_name   TEXT,
                service_date   TEXT,
                arrival_window INTEGER,
                frequency      TEXT,
                pricing_params TEXT,
                notes          TEXT,
                total          REAL DEFAULT 0,
                status         TEXT DEFAULT 'pending',
                status_note    TEXT DEFAULT '',
                raw_response   TEXT,
                created_at     TEXT DEFAULT (datetime('now')),
                updated_at     TEXT DEFAULT (datetime('now'))
            );
        ");

        // Seed default admin (admin / admin123)
        $exists = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if (!$exists) {
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
            $stmt->execute(['admin', $hash]);
        }

        // Seed default SEO rows
        $pages = ['home', 'booking'];
        foreach ($pages as $page) {
            $pdo->prepare("INSERT OR IGNORE INTO seo_settings (page) VALUES (?)")->execute([$page]);
        }

        // Seed default content rows
        $defaults = [
            ['hero_headline', 'Professional Cleaning <span>Services</span> You Can Trust', 'Hero Headline', 'html'],
            ['hero_subtext', 'Background-checked, insured cleaners delivering consistent results. Book online in 60 seconds — no phone calls required.', 'Hero Sub-text', 'textarea'],
            ['stat_clients', '5,000+', 'Stat — Clients Served', 'text'],
            ['stat_rating', '4.9★', 'Stat — Average Rating', 'text'],
            ['stat_guarantee', '100%', 'Stat — Satisfaction Guarantee', 'text'],
            ['stat_book_time', '60s', 'Stat — Time to Book', 'text'],
            ['cta_headline', 'Ready for a Cleaner Home?', 'CTA Headline', 'text'],
            ['cta_subtext', 'Book online in under 60 seconds. First-time clients receive 15% off their first clean.', 'CTA Sub-text', 'textarea'],
            ['discount_text', '15%', 'First-time Discount %', 'text'],
            ['footer_copyright', '© 2026 BronxHomeServices. All rights reserved.', 'Footer Copyright', 'text'],
            ['company_name', 'BronxHomeServices', 'Company Name', 'text'],
            ['company_phone', '', 'Company Phone', 'text'],
            ['company_email', '', 'Company Email', 'text'],
            ['company_address', '', 'Company Address', 'textarea'],
        ];
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_content (key, value, label, type) VALUES (?, ?, ?, ?)");
        foreach ($defaults as $row) {
            $stmt->execute($row);
        }
    }

    // ── SEO HELPERS ───────────────────────────────────────────────────────────
    public static function getSeo(string $page): array
    {
        $stmt = self::connect()->prepare("SELECT * FROM seo_settings WHERE page = ?");
        $stmt->execute([$page]);
        return $stmt->fetch() ?: ['page' => $page, 'title' => '', 'description' => '', 'keywords' => '', 'og_image' => ''];
    }

    public static function getAllSeo(): array
    {
        return self::connect()->query("SELECT * FROM seo_settings ORDER BY page")->fetchAll();
    }

    public static function saveSeo(string $page, string $title, string $description, string $keywords, string $ogImage): void
    {
        $stmt = self::connect()->prepare("
            INSERT INTO seo_settings (page, title, description, keywords, og_image, updated_at)
            VALUES (?, ?, ?, ?, ?, datetime('now'))
            ON CONFLICT(page) DO UPDATE SET
                title       = excluded.title,
                description = excluded.description,
                keywords    = excluded.keywords,
                og_image    = excluded.og_image,
                updated_at  = excluded.updated_at
        ");
        $stmt->execute([$page, $title, $description, $keywords, $ogImage]);
    }

    // ── CONTENT HELPERS ───────────────────────────────────────────────────────
    public static function getContent(?string $key = null): mixed
    {
        if ($key !== null) {
            $stmt = self::connect()->prepare("SELECT value FROM site_content WHERE key = ?");
            $stmt->execute([$key]);
            return $stmt->fetchColumn() ?: '';
        }
        $rows = self::connect()->query("SELECT key, value, label, type FROM site_content ORDER BY rowid")->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row;
        }
        return $out;
    }

    public static function saveContent(array $data): void
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare("
            UPDATE site_content SET value = ?, updated_at = datetime('now') WHERE key = ?
        ");
        foreach ($data as $key => $value) {
            $stmt->execute([trim($value), $key]);
        }
    }

    // ── DASHBOARD HELPERS ─────────────────────────────────────────────────────
    public static function getDashboardStats(): array
    {
        $pdo = self::connect();
        $today = date('Y-m-d');

        $total = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $pending = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
        $todayCnt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(created_at) = '$today'")->fetchColumn();
        $revenue = $pdo->query("SELECT SUM(total) FROM bookings")->fetchColumn();

        // Today's upcoming appointments (by service_date)
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE DATE(service_date) = ? ORDER BY service_date ASC LIMIT 10");
        $stmt->execute([$today]);
        $todayAppts = $stmt->fetchAll();

        // Recent 8 bookings
        $recent = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 8")->fetchAll();

        // Bookings per day for last 30 days
        $stmt2 = $pdo->prepare("
            SELECT DATE(created_at) as day, COUNT(*) as cnt
            FROM bookings
            WHERE created_at >= DATE('now', '-29 days')
            GROUP BY day ORDER BY day ASC
        ");
        $stmt2->execute();
        $perDay = $stmt2->fetchAll();

        return compact('total', 'pending', 'todayCnt', 'revenue', 'todayAppts', 'recent', 'perDay');
    }

    // ── BOOKING HELPERS ───────────────────────────────────────────────────────
    public static function saveBooking(array $d): int
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare("
            INSERT INTO bookings
                (l27_id, first_name, last_name, email, phone,
                 address, city, state, zip,
                 service_id, service_name, service_date, arrival_window,
                 frequency, pricing_params, notes, total, raw_response)
            VALUES
                (:l27_id, :first_name, :last_name, :email, :phone,
                 :address, :city, :state, :zip,
                 :service_id, :service_name, :service_date, :arrival_window,
                 :frequency, :pricing_params, :notes, :total, :raw_response)
        ");
        $stmt->execute([
            ':l27_id' => $d['l27_id'] ?? '',
            ':first_name' => $d['first_name'] ?? '',
            ':last_name' => $d['last_name'] ?? '',
            ':email' => $d['email'] ?? '',
            ':phone' => $d['phone'] ?? '',
            ':address' => $d['address'] ?? '',
            ':city' => $d['city'] ?? '',
            ':state' => $d['state'] ?? '',
            ':zip' => $d['zip'] ?? '',
            ':service_id' => $d['service_id'] ?? 0,
            ':service_name' => $d['service_name'] ?? '',
            ':service_date' => $d['service_date'] ?? '',
            ':arrival_window' => $d['arrival_window'] ?? 0,
            ':frequency' => $d['frequency'] ?? 'once',
            ':pricing_params' => $d['pricing_params'] ?? '',
            ':notes' => $d['notes'] ?? '',
            ':total' => $d['total'] ?? 0,
            ':raw_response' => $d['raw_response'] ?? '',
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function getBookings(?string $status = null, int $limit = 500): array
    {
        $sql = "SELECT * FROM bookings";
        $bind = [];
        if ($status && $status !== 'all') {
            $sql .= " WHERE status = :status";
            $bind[':status'] = $status;
        }
        $sql .= " ORDER BY created_at DESC LIMIT :limit";
        $stmt = self::connect()->prepare($sql);
        foreach ($bind as $k => $v)
            $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getBookingById(int $id): ?array
    {
        $stmt = self::connect()->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function updateBookingStatus(int $id, string $status, string $note = ''): void
    {
        $stmt = self::connect()->prepare("
            UPDATE bookings
            SET status = ?, status_note = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        $stmt->execute([$status, $note, $id]);
    }

    // ── VISITOR HELPERS ───────────────────────────────────────────────────────
    public static function saveVisit(array $data): void
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare("
            INSERT INTO visitors
                (ip, country, country_code, city, region, isp, page, referrer, user_agent, browser, os, device)
            VALUES
                (:ip, :country, :country_code, :city, :region, :isp, :page, :referrer, :user_agent, :browser, :os, :device)
        ");
        $stmt->execute([
            ':ip' => $data['ip'] ?? '',
            ':country' => $data['country'] ?? '',
            ':country_code' => $data['country_code'] ?? '',
            ':city' => $data['city'] ?? '',
            ':region' => $data['region'] ?? '',
            ':isp' => $data['isp'] ?? '',
            ':page' => $data['page'] ?? '',
            ':referrer' => $data['referrer'] ?? '',
            ':user_agent' => $data['user_agent'] ?? '',
            ':browser' => $data['browser'] ?? '',
            ':os' => $data['os'] ?? '',
            ':device' => $data['device'] ?? '',
        ]);
    }

    public static function getVisits(string $from, string $to, int $limit = 1000): array
    {
        $stmt = self::connect()->prepare("
            SELECT * FROM visitors
            WHERE visited_at >= :from AND visited_at <= :to
            ORDER BY visited_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':from', $from . ' 00:00:00');
        $stmt->bindValue(':to', $to . ' 23:59:59');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getVisitStats(string $from, string $to): array
    {
        $pdo = self::connect();
        $base = "FROM visitors WHERE visited_at >= :from AND visited_at <= :to";
        $bind = [':from' => $from . ' 00:00:00', ':to' => $to . ' 23:59:59'];

        $total = $pdo->prepare("SELECT COUNT(*) $base");
        $total->execute($bind);
        $unique = $pdo->prepare("SELECT COUNT(DISTINCT ip) $base");
        $unique->execute($bind);
        $topCC = $pdo->prepare("SELECT country, COUNT(*) c $base GROUP BY country ORDER BY c DESC LIMIT 1");
        $topCC->execute($bind);
        $topPage = $pdo->prepare("SELECT page, COUNT(*) c $base GROUP BY page ORDER BY c DESC LIMIT 5");
        $topPage->execute($bind);
        $topBrowser = $pdo->prepare("SELECT browser, COUNT(*) c $base GROUP BY browser ORDER BY c DESC LIMIT 5");
        $topBrowser->execute($bind);
        $perDay = $pdo->prepare("SELECT DATE(visited_at) as day, COUNT(*) as cnt $base GROUP BY day ORDER BY day ASC");
        $perDay->execute($bind);

        return [
            'total' => (int) $total->fetchColumn(),
            'unique' => (int) $unique->fetchColumn(),
            'country' => $topCC->fetchColumn() ?: '—',
            'top_page' => ($tp = $topPage->fetchAll()) ? ($tp[0]['page'] ?? '—') : '—',
            'top_pages' => $tp ?? [],
            'top_browsers' => $topBrowser->fetchAll(),
            'per_day' => $perDay->fetchAll(),
        ];
    }

    // ── AUTH HELPERS ──────────────────────────────────────────────────────────
    public static function verifyLogin(string $username, string $password): ?array
    {
        $stmt = self::connect()->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public static function updatePassword(int $id, string $newPassword): void
    {
        $stmt = self::connect()->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
        $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }
}
