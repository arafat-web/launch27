<?php
/**
 * VisitorTracker
 * ──────────────
 * Captures visitor data (IP, geo, browser, OS, page) and persists it
 * via Database::saveVisit(). All errors are silently caught so tracking
 * never breaks a public page.
 */
class VisitorTracker
{
    // ── Bot detection patterns ─────────────────────────────────────────────────
    private static array $botPatterns = [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'archiver',
        'facebookexternalhit',
        'twitterbot',
        'linkedinbot',
        'googlebot',
        'bingbot',
        'yandexbot',
        'baiduspider',
        'duckduckbot',
        'wget',
        'curl',
        'python-requests',
        'go-http-client',
        'java/',
        'scrapy',
    ];

    // ── Public entry point ─────────────────────────────────────────────────────
    public static function track(string $page): void
    {
        try {
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Skip bots
            if (self::isBot($ua))
                return;

            $ip = self::realIp();
            $geo = self::geoLookup($ip);

            Database::saveVisit([
                'ip' => $ip,
                'country' => $geo['country'] ?? '',
                'country_code' => $geo['countryCode'] ?? '',
                'city' => $geo['city'] ?? '',
                'region' => $geo['regionName'] ?? '',
                'isp' => $geo['isp'] ?? '',
                'page' => $page,
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : '',
                'user_agent' => substr($ua, 0, 500),
                'browser' => self::parseBrowser($ua),
                'os' => self::parseOs($ua),
                'device' => self::parseDevice($ua),
            ]);
        } catch (\Throwable) {
            // Silently fail — tracking must never break the public site
        }
    }

    // ── Real IP (proxy-aware) ──────────────────────────────────────────────────
    private static function realIp(): string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                // X-Forwarded-For can be a comma-separated list; take the first
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP))
                    return $ip;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    // ── Geo lookup via ip-api.com (free, no key needed) ───────────────────────
    private static function geoLookup(string $ip): array
    {
        // Don't look up private/localhost IPs
        if (self::isPrivateIp($ip)) {
            return ['country' => 'Local', 'countryCode' => 'LO', 'city' => 'Localhost', 'regionName' => '', 'isp' => ''];
        }

        $url = 'http://ip-api.com/json/' . urlencode($ip)
            . '?fields=country,countryCode,city,regionName,isp&lang=en';

        $ctx = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
        $raw = @file_get_contents($url, false, $ctx);
        if (!$raw)
            return [];

        $data = json_decode($raw, true);
        return (is_array($data) && ($data['status'] ?? '') === 'success') ? $data : [];
    }

    // ── UA parsers ─────────────────────────────────────────────────────────────
    private static function parseBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/'))
            return 'Edge';
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera'))
            return 'Opera';
        if (str_contains($ua, 'SamsungBrowser'))
            return 'Samsung Browser';
        if (str_contains($ua, 'Firefox/'))
            return 'Firefox';
        if (str_contains($ua, 'Chrome/'))
            return 'Chrome';
        if (str_contains($ua, 'Safari/') && str_contains($ua, 'Version/'))
            return 'Safari';
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident/'))
            return 'IE';
        return 'Other';
    }

    private static function parseOs(string $ua): string
    {
        if (str_contains($ua, 'Windows NT'))
            return 'Windows';
        if (str_contains($ua, 'Macintosh'))
            return 'macOS';
        if (str_contains($ua, 'iPhone'))
            return 'iOS';
        if (str_contains($ua, 'iPad'))
            return 'iPadOS';
        if (str_contains($ua, 'Android'))
            return 'Android';
        if (str_contains($ua, 'Linux'))
            return 'Linux';
        if (str_contains($ua, 'CrOS'))
            return 'ChromeOS';
        return 'Other';
    }

    private static function parseDevice(string $ua): string
    {
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'iPhone'))
            return 'Mobile';
        if (str_contains($ua, 'iPad') || str_contains($ua, 'Tablet'))
            return 'Tablet';
        return 'Desktop';
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    private static function isBot(string $ua): bool
    {
        $lower = strtolower($ua);
        foreach (self::$botPatterns as $p) {
            if (str_contains($lower, $p))
                return true;
        }
        return empty($ua);
    }

    private static function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
