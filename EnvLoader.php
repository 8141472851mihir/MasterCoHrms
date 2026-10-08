<?php
/**
 * Environment Loader Class
 * Handles loading and parsing of .env files
 */
class EnvLoader
{
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     * @param string $path Path to .env file
     * @return bool
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return true;
        }

        if ($path === null) {
            $path = dirname(dirname(__DIR__)) . '/.env';
        }

        if (!file_exists($path)) {
            error_log("Environment file not found: {$path}");
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                self::$variables[$key] = $value;
            }
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Get environment variable
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]) ? self::$variables[$key] : $default;
    }

    /**
     * Set environment variable
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
    }

    /**
     * Check if environment variable exists
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]);
    }

    /**
     * Get all environment variables
     * @return array
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }

    /**
     * Load environment variables and make them available as constants
     * @param string $prefix
     */
    public static function loadAsConstants($prefix = 'ENV_')
    {
        if (!self::$loaded) {
            self::load();
        }

        foreach (self::$variables as $key => $value) {
            $constantName = $prefix . strtoupper($key);
            if (!defined($constantName)) {
                define($constantName, $value);
            }
        }
    }
}
?>