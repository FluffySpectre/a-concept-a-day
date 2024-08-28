<?php 

namespace DA;

/**
 * Implements reading from the system config file
 *
 * @author Björn Bosse
 */
class Config {
    private static $config;

    /**
     * Reads a value from the config file
     * 
     * @param string $key      The config key
     * @param string $default  The value returned, if no value for the given key was found
     * @return mixed           The config value or default value
     */
    public static function get($key, $default = null) {
        $config = self::getConfig();
        return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * Returns the complete config assoc array
     * 
     * @return array The config array
     */
    public static function getAll() {
        return self::getConfig();
    }

    /**
     * Loads the values from the .env file and returns it
     * 
     * @return array The config array
     */
    private static function getConfig() {
        if (is_null(self::$config)) {
            self::$config = parse_ini_file(__DIR__ . "/../.env");
        }
        return self::$config;
    }
}
