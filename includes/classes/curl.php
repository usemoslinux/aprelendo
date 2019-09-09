<?php

namespace Aprelendo\Includes\Classes;

/**
 * Curl trait
 */
trait Curl
{
    // private static $proxy = 'www-proxy.mrec.ar:8080';
    private static $proxy = '';

    /**
     * Get file contents using curl
     * @param string $url
     */
    public static function get_url_contents ($url) {
        $ch = curl_init();
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow 301 redirects
        curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        // $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // used to check possible errors
        $result = curl_exec($ch);

        curl_close($ch); 

        return $result ? $result : '';
    }
}
