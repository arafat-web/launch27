<?php
/**
 * Auth
 * ────
 * Session-based authentication helpers.
 */
class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['admin_user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
        ];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        self::start();
        return !empty($_SESSION['admin_user']);
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['admin_user'] ?? null;
    }

    /** Redirect to login if not authenticated. */
    public static function guard(): void
    {
        if (!self::check()) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            header('Location: ' . $base . '/admin/login');
            exit;
        }
    }

    public static function redirect(string $path): never
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        header('Location: ' . $base . $path);
        exit;
    }
}
