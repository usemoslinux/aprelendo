<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class Cache
{
    private static ?string $cache_dir = null;

    /**
     * Set a value in the cache.
     *
     * @param string $key Unique key for the cache file.
     * @param mixed $data Data to be stored (will be serialized).
     * @return void
     */
    public static function set(string $key, $data): void
    {
        $cache_dir = self::cacheDir();

        $file_path = $cache_dir . $key . '.cache';
        if (@file_put_contents($file_path, serialize($data), LOCK_EX) === false) {
            throw new \RuntimeException("Unable to write cache file: {$file_path}");
        }
    }

    /**
     * Get a value from the cache.
     *
     * @param string $key Unique key for the cache file.
     * @param int $ttl Time to live in seconds (default: 1 week).
     * @return mixed The cached data or null if not found/expired.
     */
    public static function get(string $key, int $ttl = 604800)
    {
        $file_path = self::cacheDir() . $key . '.cache';

        if (file_exists($file_path)) {
            // Check if the file is still valid (not expired)
            if ((time() - filemtime($file_path)) < $ttl) {
                $content = file_get_contents($file_path);
                return unserialize($content);
            }
            
            // If expired, remove it
            unlink($file_path);
        }

        return null;
    }

    /**
     * Delete a specific cache file.
     *
     * @param string $key Unique key for the cache file.
     * @return void
     */
    public static function delete(string $key): void
    {
        $file_path = self::cacheDir() . $key . '.cache';
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    /**
     * Clear all cache files.
     *
     * @return void
     */
    public static function clearAll(): void
    {
        $cache_dir = self::cacheDir();

        if (is_dir($cache_dir)) {
            $files = glob($cache_dir . '*.cache');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    private static function cacheDir(): string
    {
        if (self::$cache_dir !== null) {
            return self::$cache_dir;
        }

        $cache_dir = defined('CACHE_PATH') ? CACHE_PATH : '/var/cache/aprelendo/';
        $cache_dir = rtrim($cache_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($cache_dir) && !@mkdir($cache_dir, 0755, true) && !is_dir($cache_dir)) {
            throw new \RuntimeException("Unable to create cache directory: {$cache_dir}");
        }

        if (!is_writable($cache_dir)) {
            throw new \RuntimeException("Cache directory is not writable: {$cache_dir}");
        }

        self::$cache_dir = $cache_dir;

        return self::$cache_dir;
    }
}
