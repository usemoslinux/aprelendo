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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

// App paths
defined('APP_ROOT')     ? null : define('APP_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
defined('PUBLIC_PATH')  ? null : define('PUBLIC_PATH', APP_ROOT . 'public' . DIRECTORY_SEPARATOR);
defined('UPLOADS_PATH')
    ? null
    : define('UPLOADS_PATH', dirname(APP_ROOT) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
defined('TEMPLATES_PATH')
    ? null
    : define('TEMPLATES_PATH', dirname(APP_ROOT) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);

// Database
defined('DB_DRIVER')              ? null : define('DB_DRIVER', 'mysql');
defined('DB_HOST')                ? null : define('DB_HOST', 'mysql');
defined('DB_NAME')                ? null : define('DB_NAME', 'aprelendo');
defined('DB_USER')                ? null : define('DB_USER', 'aprelendo_user'); // << update this
defined('DB_PASSWORD')            ? null : define('DB_PASSWORD', 'aprelendo_user_password'); // << update this
defined('DB_CHARSET')             ? null : define('DB_CHARSET', 'utf8mb4');

// YouTube API key used to retrieve YouTube videos
defined('YOUTUBE_API_KEY')        ? null : define('YOUTUBE_API_KEY', 'your_youtube_api_key'); // << update this
// Google Drive API key used to support audio for ebooks
defined('GOOGLE_DRIVE_API_KEY')   ? null : define('GOOGLE_DRIVE_API_KEY', 'your_google_api_key'); // << update this
// VoiceRSS API key to provide TTS support in assisted learning mode (voicerss.org)
defined('VOICERSS_API_KEY')       ? null : define('VOICERSS_API_KEY', 'your_voicerss_api_key'); // << update this

// Email
defined('EMAIL_HOST')             ? null : define('EMAIL_HOST', 'EMAIL_HOST'); // << update this
defined('EMAIL_SENDER')           ? null : define('EMAIL_SENDER', 'Sender <example@mail.com>'); // << update this
defined('EMAIL_SENDER_USERNAME')  ? null : define('EMAIL_SENDER_USERNAME', 'USER'); // << update this
defined('EMAIL_SENDER_PASSWORD')  ? null : define('EMAIL_SENDER_PASSWORD', 'PASSWORD'); // << update this
defined('SUPPORT_EMAIL')          ? null : define('SUPPORT_EMAIL', 'example@mail.com'); // << update this

defined('MOCK_USER_AGENT')        ? null : define('MOCK_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
    .'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
defined('PYTHON_VENV')            ? null : define('PYTHON_VENV', '/opt/venv');

defined('PROXY')                  ? null : define('PROXY', '');

define('ENCRYPTION_KEY', 'Replace this with a nice encryption key'); // << update this
