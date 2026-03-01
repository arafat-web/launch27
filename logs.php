<?php
$LOG_DIR = __DIR__ . '/logs';

// Get logs
function get_all_logs($days = 7) {
    global $LOG_DIR;
    
    $logs = [];
    
    for ($i = 0; $i < $days; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $log_file = $LOG_DIR . '/' . $date . '_bookings.log';
        
        if (file_exists($log_file)) {
            $file_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($file_lines as $line) {
                if (!empty($line)) {
                    $logs[] = json_decode($line, true);
                }
            }
        }
    }
    
    return array_reverse($logs);
}

$sort = $_GET['sort'] ?? 'newest';
$filter = $_GET['filter'] ?? 'all';
$days = $_GET['days'] ?? 7;

$all_logs = get_all_logs((int)$days);

// Filter logs
if ($filter !== 'all') {
    $all_logs = array_filter($all_logs, function($log) use ($filter) {
        return $log['status'] === $filter;
    });
}

if ($sort === 'oldest') {
    $all_logs = array_reverse($all_logs);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Launch27 Booking Logs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        label {
            font-weight: 500;
            color: #666;
        }
        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            padding: 8px 16px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        button:hover {
            background: #0052a3;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .stat {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #0066cc;
        }
        .stat-label {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        .logs {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .log-entry {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: grid;
            grid-template-columns: 150px 1fr 100px 80px;
            gap: 15px;
            align-items: center;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .log-timestamp {
            font-size: 12px;
            color: #999;
            font-family: monospace;
        }
        .log-type {
            font-weight: 600;
            color: #333;
        }
        .log-data {
            color: #666;
            font-size: 13px;
            max-height: 60px;
            overflow-y: auto;
        }
        .log-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .empty {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0066cc;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .log-entry {
                grid-template-columns: 1fr;
            }
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Back to Booking Form</a>
        
        <div class="header">
            <h1>📋 Launch27 Booking Logs</h1>
            
            <div class="filters">
                <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div class="filter-group">
                        <label>Filter:</label>
                        <select name="filter" onchange="this.form.submit()">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Events</option>
                            <option value="SUCCESS" <?= $filter === 'SUCCESS' ? 'selected' : '' ?>>✓ Success</option>
                            <option value="ERROR" <?= $filter === 'ERROR' ? 'selected' : '' ?>>✗ Errors</option>
                            <option value="INFO" <?= $filter === 'INFO' ? 'selected' : '' ?>>ℹ Info</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Sort:</label>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Last:</label>
                        <select name="days" onchange="this.form.submit()">
                            <option value="1" <?= $days === '1' ? 'selected' : '' ?>>1 Day</option>
                            <option value="7" <?= $days === '7' ? 'selected' : '' ?>>7 Days</option>
                            <option value="30" <?= $days === '30' ? 'selected' : '' ?>>30 Days</option>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="stats">
                <?php 
                $total = count($all_logs);
                $success = count(array_filter($all_logs, fn($l) => $l['status'] === 'SUCCESS'));
                $errors = count(array_filter($all_logs, fn($l) => $l['status'] === 'ERROR'));
                $bookings = count(array_filter($all_logs, fn($l) => $l['type'] === 'BOOKING_SUCCESS'));
                ?>
                <div class="stat">
                    <div class="stat-label">Total Events</div>
                    <div class="stat-value"><?= $total ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Successful</div>
                    <div class="stat-value"><?= $success ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Errors</div>
                    <div class="stat-value"><?= $errors ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Bookings Created</div>
                    <div class="stat-value"><?= $bookings ?></div>
                </div>
            </div>
        </div>
        
        <div class="logs">
            <?php if (empty($all_logs)): ?>
                <div class="empty">
                    <p>No logs found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($all_logs as $log): ?>
                    <div class="log-entry">
                        <div class="log-timestamp"><?= $log['timestamp'] ?></div>
                        <div>
                            <div class="log-type"><?= $log['type'] ?></div>
                            <div class="log-data">
                                <?php 
                                $data_str = '';
                                if (isset($log['data']['email'])) {
                                    $data_str .= '📧 ' . htmlspecialchars($log['data']['email']) . ' ';
                                }
                                if (isset($log['data']['booking_id'])) {
                                    $data_str .= '🎫 #' . htmlspecialchars($log['data']['booking_id']) . ' ';
                                }
                                if (isset($log['data']['endpoint'])) {
                                    $data_str .= '→ ' . htmlspecialchars($log['data']['endpoint']) . ' ';
                                }
                                if (isset($log['data']['status_code'])) {
                                    $data_str .= '[' . $log['data']['status_code'] . '] ';
                                }
                                if (isset($log['data']['error'])) {
                                    $data_str .= '⚠️ ' . htmlspecialchars($log['data']['error']);
                                }
                                echo $data_str ?: '(no data)';
                                ?>
                            </div>
                        </div>
                        <div>
                            <span class="log-status status-<?= strtolower($log['status']) ?>">
                                <?= $log['status'] ?>
                            </span>
                        </div>
                        <div style="text-align: right; color: #999; font-size: 12px;">
                            <?= $log['ip'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
