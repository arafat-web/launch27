<?php
/**
 * Logger
 * ──────
 * Appends JSON log entries to a daily log file in LOG_DIR.
 */
class Logger
{
    public static function log(string $type, array|string $data, string $status = 'INFO'): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type'      => $type,
            'status'    => $status,
            'data'      => is_array($data) ? $data : ['message' => $data],
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ];

        $file = LOG_DIR . '/' . date('Y-m-d') . '_bookings.log';
        file_put_contents($file, json_encode($entry) . "\n", FILE_APPEND | LOCK_EX);
    }

    public static function getLogs(int $lines = 100): array
    {
        $file = LOG_DIR . '/' . date('Y-m-d') . '_bookings.log';
        if (!file_exists($file)) {
            return ['error' => 'No logs for today'];
        }

        $all = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $all = array_slice($all, -$lines);

        $logs = [];
        foreach ($all as $line) {
            if (!empty($line)) {
                $logs[] = json_decode($line, true);
            }
        }

        return array_reverse($logs);
    }
}
