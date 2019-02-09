<?php
/**
 * Copyright (C) 2018 Pablo Castagnino
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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('DB_SERVER')    ? null : define('DB_SERVER', 'localhost');
defined('DB_NAME')      ? null : define('DB_NAME', 'aprelendo');
defined('DB_USER')      ? null : define('DB_USER', 'username');
defined('DB_PASSWORD')  ? null : define('DB_PASSWORD', 'password');
defined('DB_CHARSET')   ? null : define('DB_CHARSET', 'utf8');

defined('YOUTUBE_API_KEY')  ? null : define('YOUTUBE_API_KEY', 'your_youtube_api_key');
defined('VOICERSS_API_KEY')  ? null : define('VOICERSS_API_KEY', 'your_voicerss_api_key');

defined('EMAIL_SENDER')     ? null : define('EMAIL_SENDER', 'example@mail.com');

?>