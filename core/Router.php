<?php
/**
 * Router
 * ──────
 * Matches the incoming HTTP method + URI path against the route table
 * and dispatches to the appropriate controller method.
 */
class Router
{
    public function __construct(private array $routes) {}

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip sub-folder prefix if the app lives in a sub-directory (e.g. /launch27)
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = '/' . ltrim($uri, '/');
        $uri = rtrim($uri, '/') ?: '/';

        $key = $method . ' ' . $uri;

        if (isset($this->routes[$key])) {
            [$controllerName, $action] = $this->routes[$key];
            $controller = new $controllerName();
            $controller->$action();
            return;
        }

        // 404
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title>
        <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#F1F5F9;}
        .box{text-align:center;}.box h1{font-size:4rem;color:#0F172A;margin:0;}.box p{color:#64748B;margin:8px 0 24px;}
        .box a{background:#2563EB;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:700;}</style>
        </head><body><div class="box"><h1>404</h1><p>The page you are looking for does not exist.</p>
        <a href="/">← Back to Home</a></div></body></html>';
    }
}
