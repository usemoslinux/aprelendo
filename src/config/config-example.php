<?php
// SPDX-License-Identifier: GPL-3.0-or-later

// App paths
define('APP_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', APP_ROOT . 'public' . DIRECTORY_SEPARATOR);
define('UPLOADS_PATH', dirname(APP_ROOT) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('TEMPLATES_PATH', dirname(APP_ROOT) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
define('CACHE_PATH', '/var/cache/aprelendo' . DIRECTORY_SEPARATOR);

// Database
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'mysql');
define('DB_NAME', 'aprelendo');
define('DB_USER', 'aprelendo_user'); // << update this
define('DB_PASSWORD', 'aprelendo_user_password'); // << update this
define('DB_CHARSET', 'utf8mb4');

// YouTube API key used to retrieve YouTube videos
define('YOUTUBE_API_KEY', 'your_youtube_api_key'); // << update this
// Google Drive API key used to support audio for ebooks
define('GOOGLE_DRIVE_API_KEY', 'your_google_api_key'); // << update this
// Google Sign-In web client ID used by the login page and backend audience checks
define('GOOGLE_CLIENT_ID', 'your_google_client_id.apps.googleusercontent.com'); // << update this
// VoiceRSS API key to provide TTS support in assisted learning mode (voicerss.org)
define('VOICERSS_API_KEY', 'your_voicerss_api_key'); // << update this

define('IS_SELF_HOSTED', ($_SERVER['HTTP_HOST'] ?? 'localhost') !== 'www.aprelendo.com');

// Email (shouldn't be necessary to set these if IS_SELF_HOSTED is TRUE)
define('EMAIL_HOST', 'EMAIL_HOST');
define('EMAIL_SENDER', 'Sender <example@mail.com>');
define('EMAIL_SENDER_USERNAME', 'USER');
define('EMAIL_SENDER_PASSWORD', 'PASSWORD');
define('SUPPORT_EMAIL', 'example@mail.com');

define('MOCK_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
    .'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
define('PYTHON_VENV', '/opt/venv');

define('PROXY', '');

define('ENCRYPTION_KEY', 'Replace this with a nice encryption key'); // << update this
