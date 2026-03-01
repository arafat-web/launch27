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
        if (self::$pdo !== null) return self::$pdo;

        $dbPath = ROOT_DIR . '/database/launch27.db';
        if (!is_dir(ROOT_DIR . '/database')) {
            mkdir(ROOT_DIR . '/database', 0755, true);
        }

        self::$pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
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
            ['hero_headline',     'Professional Cleaning <span>Services</span> You Can Trust', 'Hero Headline',         'html'],
            ['hero_subtext',      'Background-checked, insured cleaners delivering consistent results. Book online in 60 seconds — no phone calls required.', 'Hero Sub-text', 'textarea'],
            ['stat_clients',      '5,000+',  'Stat — Clients Served',        'text'],
            ['stat_rating',       '4.9★',    'Stat — Average Rating',         'text'],
            ['stat_guarantee',    '100%',    'Stat — Satisfaction Guarantee', 'text'],
            ['stat_book_time',    '60s',     'Stat — Time to Book',           'text'],
            ['cta_headline',      'Ready for a Cleaner Home?', 'CTA Headline',           'text'],
            ['cta_subtext',       'Book online in under 60 seconds. First-time clients receive 15% off their first clean.', 'CTA Sub-text', 'textarea'],
            ['discount_text',     '15%',     'First-time Discount %',         'text'],
            ['footer_copyright',  '© 2026 Clean27. All rights reserved.', 'Footer Copyright', 'text'],
            ['company_name',      'Clean27', 'Company Name',                  'text'],
            ['company_phone',     '',        'Company Phone',                  'text'],
            ['company_email',     '',        'Company Email',                  'text'],
            ['company_address',   '',        'Company Address',                'textarea'],
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
        $pdo  = self::connect();
        $stmt = $pdo->prepare("
            UPDATE site_content SET value = ?, updated_at = datetime('now') WHERE key = ?
        ");
        foreach ($data as $key => $value) {
            $stmt->execute([trim($value), $key]);
        }
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
