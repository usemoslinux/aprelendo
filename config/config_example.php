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

defined('DB_DRIVER')              ? null : define('DB_DRIVER', 'mysql');
defined('DB_HOST')                ? null : define('DB_HOST', 'mysql');
defined('DB_NAME')                ? null : define('DB_NAME', 'aprelendo');
defined('DB_USER')                ? null : define('DB_USER', 'username');
defined('DB_PASSWORD')            ? null : define('DB_PASSWORD', 'password');
defined('DB_CHARSET')             ? null : define('DB_CHARSET', 'utf8mb4');

// YouTube API key used to retrieve YouTube videos 
defined('YOUTUBE_API_KEY')        ? null : define('YOUTUBE_API_KEY', 'your_youtube_api_key');
// Google Drive API key used to support audio for ebooks
defined('GOOGLE_DRIVE_API_KEY')   ? null : define('GOOGLE_DRIVE_API_KEY', 'your_google_api_key');
// VoiceRSS API key to provide TTS support in assisted learning mode
defined('VOICERSS_API_KEY')       ? null : define('VOICERSS_API_KEY', 'your_voicerss_api_key');

defined('EMAIL_SENDER')           ? null : define('EMAIL_SENDER', 'Sender <example@mail.com>');
defined('SUPPORT_EMAIL')          ? null : define('SUPPORT_EMAIL', 'example@mail.com');

defined('FICTIONAL_USER_AGENT')   ? null : define('FICTIONAL_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
    . 'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');

defined('PROXY')                  ? null : define('PROXY', '');
