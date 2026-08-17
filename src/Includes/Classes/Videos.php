<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class Videos extends DBEntity
{
    public $id                = '';
    public $user_id           = 0;
    public $lang_id           = 0;
    public $lang              = '';
    public $title             = '';
    public $author            = '';
    public $transcript_xml    = '';
    public $source_url        = '';
    public $date_created      = '';
    public $youtube_id        = '';
    public ?int $text_creation_method_id = null;
    private const YT_DESKTOP_BASE_URL = 'https://www.youtube.com/watch?v=';
    private const YT_REGEX = '#(?:youtube\.com/watch\?v=|youtu\.be/)([^&?/\s]{11})#i';

    /**
     * Constructor
     *
     * Sets 3 basic variables used to identify videos: $pdo, $user_id & lang_id
     *
     * @param \PDO $pdo
     * @param int $user_id
     * @param int $lang_id
     */
    public function __construct(\PDO $pdo, int $user_id, int $lang_id)
    {
        parent::__construct($pdo);
        $this->table = 'shared_texts';
        $this->user_id = $user_id;
        $this->lang_id = $lang_id;
    } 

    /**
     * Fetches video from YouTube
     *
     * @param string $lang ISO representation of the video's language
     * @param string $youtube_id YouTube video ID
     * @return array Array representation of video's metadata and subtitles
     */
    public function fetchVideo(string $lang, string $youtube_id): array
    {
        header('Content-Type: application/json');
        $this->lang = $lang;

        $transcript = $this->fetchTranscript($youtube_id);
        $metadata = $this->fetchVideoMetadata($youtube_id);

        // Combine metadata & transcript in a single array for response
        $result = array_merge($metadata, [
            'text' => $transcript['xml']->asXML(),
            'text_creation_method_id' => $transcript['creation_method']->value,
            'transcript_language_code' => $transcript['language_code'],
        ]);

        return $result;
    }

    /**
      * Fetch transcript XML and provenance for the given YouTube video ID
      *
      * @param string $youtube_id YouTube video ID
      * @return array Transcript XML and metadata
      */
    private function fetchTranscript(string $youtube_id): array
    {
        $error_message = "The video might lack subtitles or they're unavailable in the desired language. "
            . "Consider using the <a href='" . $this->getFilmotUrl()
            . "' target='_blank' class='alert-link'>Filmot search engine</a> to find another video.";

        if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $youtube_id)) {
            throw new UserException('Invalid video ID.');
        }

        $command = [
            PYTHON_VENV . '/bin/python',
            APP_ROOT . 'scripts/fetch-transcript.py',
            $youtube_id,
            $this->lang
        ];

        $descriptor_spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptor_spec, $pipes);

        if (!is_resource($process)) {
            throw new InternalException();
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $status = proc_close($process);

        if ($status !== 0) {
            throw new UserException($error_message);
        }

        $transcript_result = json_decode($stdout, true);

        if (!is_array($transcript_result)
            || !isset($transcript_result['is_generated'], $transcript_result['language_code'])
            || !is_bool($transcript_result['is_generated'])
            || !is_string($transcript_result['language_code'])
            || empty($transcript_result['snippets'])
            || !is_array($transcript_result['snippets'])) {
            throw new UserException($error_message);
        }

        // Convert transcript array to XML
        $transcript_xml = new \SimpleXMLElement('<root/>');
        Conversion::arrayToXml($transcript_result['snippets'], $transcript_xml);

        return [
            'xml' => $transcript_xml,
            'creation_method' => $transcript_result['is_generated']
                ? CreationMethod::machine
                : CreationMethod::human,
            'language_code' => $transcript_result['language_code'],
        ];
    }

    /**
     * Get Filmot DB URL for the current language
     *
     * @return string
     */
    private function getFilmotUrl(): string
    {
        return "https://filmot.com/captionLanguageSearch?captionLanguages=" . $this->lang
            . "&sortField=viewcount&sortOrder=desc&capLangExactMatch=1";
    } 

    /**
     * Fetch video metadata (title and author) using YouTube API
     *
     * @param string $youtube_id YouTube video ID
     * @return array Video metadata (title & channel title, used as author)
     */
    private function fetchVideoMetadata(string $youtube_id): array
    {
        $metadata = [];

        $file = Curl::getUrlContents("https://www.googleapis.com/youtube/v3/videos?id=$youtube_id&key="
            . YOUTUBE_API_KEY . "&part=snippet");
        $file = json_decode($file, true);

        if (isset($file['items'][0]['snippet'])) {
            $metadata['title'] = $file['items'][0]['snippet']['title'];
            $metadata['author'] = $file['items'][0]['snippet']['channelTitle'];
        }

        return $metadata;
    }

    /**
     * Extract YouTube Id from a given URL
     *
     * @param string $url
     * @return string YouTube Id string
     * @throws UserException
     */
    public static function extractYTId(string $url): string
    {
        if (preg_match(self::YT_REGEX, $url, $matches)) {
            return $matches[1];
        }

        throw new UserException('Malformed YouTube link');
    } 

    /**
     * Check if a given URL is a valid YouTube video link
     *
     * @param string $url
     * @return bool
     */
    public static function isYTVideo(string $url): bool
    {
        return (bool) preg_match(self::YT_REGEX, $url);
    } 

    /**
     * Converts any supported YouTube URL (mobile, shortened) 
     * into a standard desktop URL.
     */
    public static function toDesktopUrl(string $url): string
    {
        $id = self::extractYTId($url);
        return self::YT_DESKTOP_BASE_URL . $id;
    } 

    /**
     * Loads video record data by Id
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `id`=?";
        $row = $this->sqlFetch($sql, [$id]);

        if ($row) {
            $this->id             = $row['id'];
            $this->user_id        = $row['user_id'];
            $this->lang_id        = $row['lang_id'];
            $this->title          = $row['title'];
            $this->author         = $row['author'];
            $this->transcript_xml = $row['text'];
            $this->source_url     = $row['source_uri'];
            $this->date_created   = $row['date_created'];
            $this->text_creation_method_id = isset($row['text_creation_method_id'])
                ? (int)$row['text_creation_method_id']
                : null;
            $this->youtube_id     = self::extractYTId($this->source_url);
        }
    } 
}
