<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

final class Database
{
    private static ?\PDO $pdo = null;

    /**
     * Returns the shared PDO connection for the current request.
     *
     * @return \PDO
     */
    public static function connection(): \PDO
    {
        if (self::$pdo === null) {
            try {
                $db_connection = new Connect(DB_DRIVER, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_CHARSET);
                self::$pdo = $db_connection->connect();
            } catch (\Exception $e) {
                http_response_code(500);
                exit;
            }
        }

        return self::$pdo;
    }
}
