<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

class Cache
{
    private static string $cache_dir = APP_ROOT . 'Includes/Cache/';

    /**
     * Set a value in the cache.
     *
     * @param string $key Unique key for the cache file.
     * @param mixed $data Data to be stored (will be serialized).
     * @return void
     */
    public static function set(string $key, $data): void
    {
        if (!is_dir(self::$cache_dir)) {
            mkdir(self::$cache_dir, 0755, true);
        }

        $file_path = self::$cache_dir . $key . '.cache';
        file_put_contents($file_path, serialize($data));
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
        $file_path = self::$cache_dir . $key . '.cache';

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
        $file_path = self::$cache_dir . $key . '.cache';
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
        if (is_dir(self::$cache_dir)) {
            $files = glob(self::$cache_dir . '*.cache');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
