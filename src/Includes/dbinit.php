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

// perform all database initialization here, in a single file

require_once dirname(dirname(__FILE__)) . '/config/config.php';
require_once dirname(APP_ROOT) . '/vendor/autoload.php';

use Aprelendo\Connect;

try {
    $db_connection = new Connect(DB_DRIVER, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_CHARSET);
    $pdo = $db_connection->connect();
} catch (\Exception $e) {
    http_response_code(500);
    exit;
}
