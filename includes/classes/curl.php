<?php

namespace Aprelendo\Includes\Classes;

/**
 * Curl trait
 */
trait Curl
{
    private static $proxy = 'www-proxy.mrec.ar:8080';

    /**
     * Get file contents using curl
     * @param string $url
     */
    public function get_contents ($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        $result = curl_exec($ch);
        curl_close($ch); 

        return $result ? $result : '';
    }
}
