<?php
/**
 * View
 * ────
 * Simple template renderer.
 * Includes layouts/main.php and injects the view's output as $content.
 */
class View
{
    /**
     * @param string $template  e.g. 'home/index' or 'booking/index'
     * @param array  $data      Variables to extract into the template scope
     */
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        // Capture the view body
        ob_start();
        $viewFile = VIEW_DIR . '/' . $template . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$viewFile}");
        }
        include $viewFile;
        $content = ob_get_clean();

        // Render the layout (which outputs $content)
        $layout = VIEW_DIR . '/layouts/main.php';
        if (!file_exists($layout)) {
            throw new RuntimeException("Layout not found: {$layout}");
        }
        include $layout;
    }

    /**
     * Return the base URL for assets (css/js)
     * Works whether the app is at the domain root or in a sub-folder.
     */
    public static function asset(string $path): string
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        return $base . '/public/' . ltrim($path, '/');
    }

    /**
     * Return a URL for a given route path.
     */
    public static function url(string $path = '/'): string
    {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        return $base . '/' . ltrim($path, '/');
    }
}
