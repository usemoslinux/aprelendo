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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\DBEntity;
use Aprelendo\Includes\Classes\Curl;
use Aprelendo\Includes\Classes\Conversion;
use Aprelendo\Includes\Classes\AprelendoException;

class Videos extends DBEntity
{
    private $id             = '';
    private $lang_id        = 0;
    private $lang           = '';
    private $title          = '';
    private $author         = '';
    private $youtube_id     = '';
    private $source_url     = '';
    private $transcript_xml = '';
    private $lang_codes = [
        'ar'       => 'Arabic',
        'ar-DZ'    => 'Arabic (Algeria)',
        'ar-BH'    => 'Arabic (Bahrain)',
        'ar-EG'    => 'Arabic (Egypt)',
        'ar-IQ'    => 'Arabic (Iraq)',
        'ar-JO'    => 'Arabic (Jordan)',
        'ar-KW'    => 'Arabic (Kuwait)',
        'ar-LB'    => 'Arabic (Lebanon)',
        'ar-LY'    => 'Arabic (Libya)',
        'ar-MA'    => 'Arabic (Morocco)',
        'ar-OM'    => 'Arabic (Oman)',
        'ar-QA'    => 'Arabic (Qatar)',
        'ar-SA'    => 'Arabic (Saudi Arabia)',
        'ar-SY'    => 'Arabic (Syria)',
        'ar-TN'    => 'Arabic (Tunisia)',
        'ar-AE'    => 'Arabic (U.A.E.)',
        'ar-YE'    => 'Arabic (Yemen)',
        'de'       => 'German (Standard)',
        'de-AT'    => 'German (Austria)',
        'de-CH'    => 'German (Switzerland)',
        'de-LUX'    => 'German (Luxembourg)',
        'de-LI'    => 'German (Liechtenstein)',
        'el'       => 'Greek',
        'en'       => 'English',
        'en-GB'    => 'English (United Kingdom)',
        'en-US'    => 'English (United States)',
        'en-AU'    => 'English (Australia)',
        'en-BZ'    => 'English (Belize)',
        'en-CA'    => 'English (Canada)',
        'en-IN'    => 'English (India)',
        'en-IE'    => 'English (Ireland)',
        'en-JM'    => 'English (Jamaica)',
        'en-NZ'    => 'English (New Zealand)',
        'en-ZA'    => 'English (South Africa)',
        'en-TT'    => 'English (Trinidad)',
        'es'       => 'Spanish (Spain)',
        'es-419'   => 'Spanish (Latin America)',
        'es-AR'    => 'Spanish (Argentina)',
        'es-BO'    => 'Spanish (Bolivia)',
        'es-CL'    => 'Spanish (Chile)',
        'es-CO'    => 'Spanish (Colombia)',
        'es-CR'    => 'Spanish (Costa Rica)',
        'es-DO'    => 'Spanish (Dominican Republic)',
        'es-EC'    => 'Spanish (Ecuador)',
        'es-SV'    => 'Spanish (El Salvador)',
        'es-GT'    => 'Spanish (Guatemala)',
        'es-HN'    => 'Spanish (Honduras)',
        'es-MX'    => 'Spanish (Mexico)',
        'es-NI'    => 'Spanish (Nicaragua)',
        'es-PA'    => 'Spanish (Panama)',
        'es-PY'    => 'Spanish (Paraguay)',
        'es-PE'    => 'Spanish (Peru)',
        'es-PR'    => 'Spanish (Puerto Rico)',
        'fr'       => 'French (Standard)',
        'fr-BE'    => 'French (Belgium)',
        'fr-CA'    => 'French (Canada)',
        'fr-CH'    => 'French (Switzerland)',
        'fr-LU'    => 'French (Luxembourg)',
        'he'       => 'Hebrew',
        'hi'       => 'Hindi',
        'it'       => 'Italian (Standard)',
        'it-CH'    => 'Italian (Switzerland)',
        'ja'       => 'Japanese',
        'ko'       => 'Korean',
        'nl'       => 'Dutch (Standard)',
        'nl-BE'    => 'Dutch (Belgium)',
        'es'       => 'Spanish',
        'pt'       => 'Portuguese (Portugal)',
        'pt-BR'    => 'Portuguese (Brazil)',
        'ru'       => 'Russian',
        'ru-MD'    => 'Russian (Republic of Moldova)',
        'zh'       => 'Chinese',
        'zh-CN'    => 'Chinese (PRC)',
        'zh-HK'    => 'Chinese (Hong Kong)',
        'zh-SG'    => 'Chinese (Singapore)',
        'zh-TW'    => 'Chinese (Taiwan)'
    ];

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
        parent::__construct($pdo, $user_id);

        $this->lang_id = $lang_id;
        $this->table = 'shared_texts';
    } // end __construct()

    /**
     * Fetches video from YouTube
     *
     * @param string $lang ISO representation of the video's language
     * @param string $youtube_id YouTube video ID
     * @return string JSON string representation of video's metadata and subtitles
     */
    public function fetchVideo(string $lang, string $youtube_id): string
    {
        header('Content-Type: application/json');
        $this->lang = $lang;

        $available_subs = $this->getAvailableSubs();
        $transcript_xml = $this->fetchTranscript($youtube_id, $available_subs);
        $metadata = $this->fetchVideoMetadata($youtube_id);

        // Combine metadata & transcript in a single array for response
        $result = array_merge($metadata, array('text' => $transcript_xml->asXML()));

        return json_encode($result);
    }

    /**
     * Get list of available subtitles based on provided language
     *
     * @return array List of language codes
     */
    private function getAvailableSubs(): array
    {
        $available_langs = array_filter($this->lang_codes, function ($key) {
            return $key == $this->lang || strpos($key, $this->lang . "-") === 0;
        }, ARRAY_FILTER_USE_KEY);

        return array_keys($available_langs);
    }

    /**
     * Fetch transcript XML for the given YouTube video ID and supported languages
     *
     * @param string $youtube_id YouTube video ID
     * @param array $available_subs List of supported languages
     * @return \SimpleXMLElement Transcript XML
     */
    private function fetchTranscript(string $youtube_id, array $available_subs): \SimpleXMLElement
    {
        // Fetch transcript using shell_exec
        $command = "youtube_transcript_api $youtube_id"
            . " --languages " . implode(" ", $available_subs)
            . " --format json --exclude-generated 2>&1";
        $output = shell_exec($command);

        $output_array = json_decode($output, true);

        if (!$output_array) {
            throw new AprelendoException("The video might lack subtitles or they're unavailable in the desired "
                . "language. Auto-generated Google subtitles are of low quality and won't work. Consider trying a "
                . "different video or using the <a href='" . $this->getFilmotUrl() . "' "
                . "target='_blank' class='alert-link'>Filmot search engine</a>  to find manually created subtitles.");
        }

        // Convert transcript to XML
        $transcript_xml = new \SimpleXMLElement('<root/>');
        if (isset($output_array)) {
            Conversion::arrayToXml($output_array[0], $transcript_xml);
        }

        return $transcript_xml;
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
    } // end getFilmotUrl()

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
     */
    public function extractYTId(string $url): string
    {
        if (preg_match('#^(https?://)?(www\.|m\.)?youtube\.com/watch\?v=([^&]+)#', $url, $matches)) {
            return $matches[3];
        } elseif (preg_match('#^https?://youtu\.be/([^?]+)#', $url, $matches)) {
            return $matches[1];
        } else {
            throw new AprelendoException('Malformed YouTube link');
        }
    } // end extractYTId()

    /**
     * Loads video record data by Id
     *
     * @param int $id
     * @return void
     */
    public function loadRecord(int $id): void
    {
        try {
            $sql = "SELECT *
                    FROM `{$this->table}`
                    WHERE `id`=?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row) {
                $this->id             = $row['id'];
                $this->lang_id        = $row['lang_id'];
                $this->author         = $row['author'];
                $this->source_url     = $row['source_uri'];
                $this->transcript_xml = $row['text'];
                $this->youtube_id     = $this->extractYTId($this->source_url);
            }
        } catch (\PDOException $e) {
            throw new AprelendoException('Error loading record from texts table.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Get the value of lang_id
     */
    public function getLangId(): int
    {
        return $this->lang_id;
    }

    /**
     * Get the value of title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of author
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Get the value of youtube_id
     */
    public function getYoutubeId(): string
    {
        return $this->youtube_id;
    }

    /**
     * Get the value of source_url
     */
    public function getSourceUrl(): string
    {
        return $this->source_url;
    }

    /**
     * Get the value of transcript_xml
     */
    public function getTranscriptXml(): string
    {
        return $this->transcript_xml;
    }
}
