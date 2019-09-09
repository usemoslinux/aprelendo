<?php
/**
 * Copyright (C) 2018 VoiceRSS (http://www.voicerss.org/sdk)
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

class VoiceRSS
{
	use Curl;

	public function speech($settings) {
	    $this->_validate($settings);
	    return $this->_request($settings);
	}

	private function _validate($settings) {
	    if (!isset($settings) || count($settings) == 0) throw new Exception('The settings are undefined');
        if (!isset($settings['key']) || empty($settings['key'])) throw new Exception('The API key is undefined');
        if (!isset($settings['src']) || empty($settings['src'])) throw new Exception('The text is undefined');
        if (!isset($settings['hl']) || empty($settings['hl'])) throw new Exception('The language is undefined');
	}

	private function _request($settings) {
    	$url = ((isset($settings['ssl']) && $settings['ssl']) ? 'https' : 'http') . '://api.voicerss.org/';
		
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, (isset($settings['b64']) && $settings['b64']) ? 0 : 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_buildRequest($settings));

		$resp = curl_exec($ch);

		curl_close($ch);
		
		$is_error = strpos($resp, 'ERROR') === 0;
	    
    	return array(
    		'error' => ($is_error) ? $resp : null,
    		'response' => (!$is_error) ? $resp: null);
	}

	private function _buildRequest($settings) {
	    return http_build_query(array(
	        'key' => isset($settings['key']) ? $settings['key'] : '',
	        'src' => isset($settings['src']) ? $settings['src'] : '',
	        'hl' => isset($settings['hl']) ? $settings['hl'] : '',
	        'r' => isset($settings['r']) ? $settings['r'] : '',
	        'c' => isset($settings['c']) ? $settings['c'] : '',
	        'f' => isset($settings['f']) ? $settings['f'] : '',
	        'ssml' => isset($settings['ssml']) ? $settings['ssml'] : '',
	        'b64' => isset($settings['b64']) ? $settings['b64'] : ''
	    ));
	}
}
?>