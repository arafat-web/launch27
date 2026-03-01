# Launch27 Booking System - Logging Documentation

## Overview
A comprehensive logging system has been added to track all bookings, API calls, and errors in your Launch27 booking integration.

## Components

### 1. **Backend Logging** (`api.php`)
- **Location**: `/logs/YYYY-MM-DD_bookings.log`
- **Log Types**:
  - `BOOKING_ATTEMPT` - When a user submits a booking
  - `BOOKING_SUCCESS` - When a booking is created successfully
  - `BOOKING_ERROR` - When a booking fails
  - `API_CALL` - When an API call is made to Launch27

- **Log Entry Format**:
```json
{
  "timestamp": "2026-03-01 06:55:43",
  "type": "BOOKING_ATTEMPT",
  "status": "INFO",
  "data": {
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "date": "2026-09-15",
    "time": "10:00 AM",
    "service_id": "1"
  },
  "ip": "127.0.0.1"
}
```

### 2. **Frontend Logging** (`index.html`)
- **Logger Object**: `Logger` utility in JavaScript
- **Methods**:
  - `Logger.info(message, data)` - Informational logs
  - `Logger.success(message, data)` - Success logs
  - `Logger.warning(message, data)` - Warning logs
  - `Logger.error(message, data)` - Error logs

- **Output**:
  - Console logs (color-coded by level)
  - SessionStorage for sending to server
  - In-form display of logs

- **Example Usage**:
```javascript
Logger.info("User selected service", { service: "Deep Cleaning", price: 220 });
Logger.success("Booking created", { booking_id: "12345" });
Logger.error("API connection failed", { error: "Network timeout" });
```

## Viewing Logs

### Method 1: Logs Viewer Page
**URL**: `http://launch27.test/logs.php`

Features:
- Filter by status (SUCCESS, ERROR, INFO)
- Sort by newest/oldest
- View logs from last 1, 7, or 30 days
- Statistics dashboard showing:
  - Total events
  - Successful bookings
  - Failed bookings
  - API errors

### Method 2: Dashboard
**URL**: `http://launch27.test/dashboard.html`

Features:
- Real-time stats
- Recent events list
- Daily event breakdown
- Auto-refresh every 30 seconds

### Method 3: Browser Console
Open your browser's Developer Tools (F12 or Cmd+Option+I) and check the Console tab for colored logs:
- 🔵 INFO logs (blue)
- ✅ SUCCESS logs (green)
- ⚠️ WARNING logs (orange)
- ❌ ERROR logs (red)

### Method 4: Direct API Access
**URL**: `http://launch27.test/api.php?action=logs&limit=100`

Returns JSON with recent logs:
```json
{
  "success": true,
  "count": 10,
  "logs": [
    {
      "timestamp": "2026-03-01T06:55:43+00:00",
      "type": "BOOKING_SUCCESS",
      "status": "SUCCESS",
      "data": { ... },
      "ip": "127.0.0.1"
    }
  ]
}
```

## Log Files

### Location
```
/logs/YYYY-MM-DD_bookings.log
```

### Examples
```
/logs/2026-03-01_bookings.log - Today's logs
/logs/2026-02-28_bookings.log - Yesterday's logs
```

### File Format
Each line is a JSON object:
```json
{"timestamp":"2026-03-01 06:55:43","type":"BOOKING_ATTEMPT",...}
{"timestamp":"2026-03-01 06:56:12","type":"API_CALL",...}
{"timestamp":"2026-03-01 06:56:15","type":"BOOKING_SUCCESS",...}
```

## Statistics Available

### In Logs Viewer (`logs.php`)
- Total Events
- Successful Bookings
- Failed Bookings
- Successful API Calls

### In Dashboard (`dashboard.html`)
- Total Bookings
- Successful Bookings
- Failed Bookings
- Total API Calls
- Recent Events (last 10)
- Daily Event Breakdown

## Debugging

### Common Issues

**1. Bookings not appearing in logs?**
- Check file permissions on `/logs/` directory
- Ensure it's writable: `chmod 755 logs/`

**2. API Calls failing?**
- Check logs for status code and error message
- Look for `API_CALL` entries with `"status": "ERROR"`

**3. No success logs?**
- Check `BOOKING_ERROR` logs to see validation errors
- Check API response in `API_CALL` entries

### Log Analysis Examples

**Find all failed bookings:**
```
Visit logs.php and filter by ERROR status
```

**Track specific user:**
Visit logs.php and check for entries with matching email address

**Check API performance:**
Search logs.php for `API_CALL` entries and review `status_code` and response times

## Best Practices

1. **Regular Monitoring**
   - Check logs.php daily for errors
   - Monitor dashboard for patterns

2. **Error Investigation**
   - Use API_CALL logs to see response from Launch27
   - Check BOOKING_ERROR for validation issues

3. **Performance Tracking**
   - Monitor response_size in API_CALL logs
   - Track booking success rate over time

4. **Data Retention**
   - Logs are organized by date
   - Keep logs for at least 30 days for analysis
   - Archive older logs periodically

## Customization

### Adding Custom Logs

In `api.php`:
```php
log_event('CUSTOM_EVENT', [
    'user_email' => 'user@example.com',
    'custom_field' => 'value'
], 'INFO');
```

In `index.html`:
```javascript
Logger.info("Custom event", {
    customField: "value",
    timestamp: new Date().toISOString()
});
```

### Changing Log Format

Edit the `log_event()` function in `api.php` to customize log structure.

## API Endpoints

### Get Logs
```
GET /api.php?action=logs&limit=100
```

**Parameters**:
- `limit` (optional): Number of recent logs to return (default: 100)

**Response**:
```json
{
  "success": true,
  "count": 10,
  "logs": [...]
}
```

## Security

- Logs are stored in plain text JSON format
- IP addresses are logged for each event
- Email addresses are logged (sensitive data)
- Consider implementing access control for logs.php
- Regularly review and archive old logs

## Troubleshooting

**Logs not updating?**
1. Verify `/logs/` directory exists and is writable
2. Check file permissions: `ls -la logs/`
3. Clear browser cache and retry booking

**Dashboard not loading?**
1. Check browser console for errors
2. Verify api.php is accessible at `/api.php?action=logs`
3. Check API response with curl:
```bash
curl http://launch27.test/api.php?action=logs
```

**Missing events?**
1. Check log file has entries: `cat logs/2026-03-01_bookings.log | wc -l`
2. Verify time/date on server is correct
3. Check for permission issues writing to logs directory

## Support

For issues with the logging system:
1. Check logs.php for error details
2. Review browser console logs (F12)
3. Check /logs/ directory contents
4. Verify all files are in place (api.php, logs.php, index.html)
