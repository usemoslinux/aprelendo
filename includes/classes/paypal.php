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

namespace Aprelendo\Includes\Classes;

class Paypal extends DBEntity
{
    use Curl;

    public $url;

    public function __construct($con, $user_id, $enable_sandbox) {
        parent::__construct($con, $user_id);
        $this->url = $enable_sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
    }

    public function verifyTransaction($data) {
        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
            $req .= "&$key=$value";
        }
    
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($ch, CURLOPT_PROXY, self::$proxy);
        $res = curl_exec($ch);

        if (!$res) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
    
        $info = curl_getinfo($ch);
    
        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new Exception("PayPal responded with http code $httpCode");
        }
    
        curl_close($ch);
    
        return $res === 'VERIFIED';
    }

    public function addPayment($data) {
        $today = date('Y-m-d H:i:s');
        // TODO: adjust date interval: 30 days for monthly subscriptions / 365 days for yearly subscriptions
        $premium_until = date('Y-m-d H:i:s', strtotime($today . ' + 30 days'));

        if (!is_array($data)) {
            return false;
        }

        // falta payment_item_id : agregar ? al final tb
        $stmt = $this->con->prepare('INSERT INTO `payments` (`user_id`, `txn_id`, `amount`, `status`, `item_id`, `date_created`) VALUES(?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            $error = $this->con->error;
        }
        $stmt->bind_param(
            'ssdsis',
            $this->user_id,
            $data['txn_id'],
            $data['payment_amount'],
            $data['payment_status'],
            $data['item_number'],
            $today
        );

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $stmt = $this->con->prepare('UPDATE `users` SET `userPremiumUntil`= ? WHERE `userId` = ?');
        $stmt->bind_param(
            'ss',
            $premium_until,
            $this->user_id
        );
        $stmt->execute();
        
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $stmt->close();
        return true;
    }

    public function checkTxnid($txn_id) {
        $txn_id = $this->con->real_escape_string($txn_id);
        $result = $this->con->query("SELECT * FROM `payments` WHERE `txn_id` = '$txn_id'");
    
        return $result === false || $result->num_rows == 0;
    }
}



?>