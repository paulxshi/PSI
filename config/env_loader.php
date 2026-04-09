<?php
<<<<<<< HEAD
=======
/**
 * Simple Environment Variable Loader
 * ===================================
 * Loads environment variables from .env file
 * This is a lightweight alternative to vlucas/phpdotenv
 */
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1

class EnvLoader {
    private static $loaded = false;
    
<<<<<<< HEAD
=======
    /**
     * Load environment variables from .env file
     * 
     * @param string $path Path to .env file
     * @return bool True if loaded successfully
     */
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    public static function load($path) {
        if (self::$loaded) {
            return true;
        }
        
        if (!file_exists($path)) {
            error_log("Environment file not found: $path");
            return false;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                // Set environment variable if not already set
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        
        self::$loaded = true;
        return true;
    }
    
<<<<<<< HEAD
=======
    /**
     * Get environment variable with optional default value
     * 
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed
     */
>>>>>>> c2e8593a1ad4020f5eae02badf0b05bef60e8cf1
    public static function get($key, $default = null) {
        // Check $_ENV first
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        // Check getenv()
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Check if environment variable exists
     * 
     * @param string $key Variable name
     * @return bool
     */
    public static function has($key) {
        return isset($_ENV[$key]) || getenv($key) !== false;
    }
}

// Auto-load .env file if it exists
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    EnvLoader::load($envPath);
}
