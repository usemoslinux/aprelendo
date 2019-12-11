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

use Aprelendo\Includes\Classes\Curl;

require_once '../config/config.php';

class Paypal extends DBEntity
{
    private $url = '';
    private $id = 0;
    private $txn_id = '';
    private $payer_id = '';
    private $subscription_id = '';
    private $amount = 0;
    private $status = '';
    private $date_created = '';

    /**
     * Constructor
     *
     * @param PDO $pdo
     * @param int $user_id
     * @param boolean $enable_sandbox Paypal sandbox for testing purposes
     */
    public function __construct(\PDO $pdo, int $user_id, bool $enable_sandbox) {
        parent::__construct($pdo, $user_id);
        $this->url = $enable_sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        $this->table = 'payments';
    } // end __construct()

    /**
     * Loads user record data
     *
     * @return void
     */
    public function loadRecordByUserId(): void {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `user_id` = ? ORDER BY date_created DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
           
            if ($row) {
                $this->id              = $row['id']; 
                $this->txn_id          = $row['txn_id']; 
                $this->txn_id          = $row['payer_id']; 
                $this->txn_id          = $row['subscription_id']; 
                $this->amount          = $row['amount'];
                $this->status          = $row['status']; 
                $this->date_created    = $row['date_created'];
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load payment record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecordByUserId()

    public function verifyTransactionIPN(array $data): bool {
        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
            $req .= "&$key=$value";
        }
    
        $curl_options = array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => array('Connection: Close')
        );

        try {
            $res = Curl::getUrlContents($this->url, $curl_options);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }    
        return $res === 'VERIFIED';
    } // end verifyTransactionIPN()

    public function verifyTransactionPDT($tx) {
        $req = 'cmd=_notify-synch';
        $tx_token = $tx;
        $auth_token = "siw1VZH10S_RgptHvNvJXJ6bhjbd8sdbfjhsbj43jju9lF7d1sKSclIC";
        $req .= '&tx=' . $tx_token . '&at=' . PAYPAL_AUTH_TOKEN;
        
        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        
        // url for paypal sandbox
        $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);    
        
        // url for payal
        // $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
        
        // If possible, securely post back to paypal using HTTPS
        
        // PHP server needs to be SSL enabled
        if ($fp) {
            fputs ($fp, $header . $req);
            
            // read the body data
            $res = '';
            $headerdone = false;
            
            while (!feof($fp)) {
                $line = fgets ($fp, 1024);
                if (strcmp($line, "\r\n") == 0) {
                    // read the header
                    $headerdone = true;
                }
                else if ($headerdone) {
                    // header has been read. now read the contents
                    $res .= $line;
                }
            }
            
            // parse the data
            $lines = explode("\n", $res);
            $response = array();
            
            if (strcmp ($lines[0], "SUCCESS") == 0) {
                for ($i=1; $i<count($lines);$i++){
                    list($key,$val) = explode("=", $lines[$i]);
                    $response[urldecode($key)] = urldecode($val);
                }
                
                $itemName = $response["item_name"];
                $amount = $response["payment_gross"];
                $paymentStatus = $response["payment_status"];
                $paypalTxId = $response["txn_id"];
                $currency = $response["mc_currency"];
                
                // check the payment_status is Completed
                if($paymentStatus!="Completed") {
                    throw new \Exception('Payment not completed.');
                }
                // check that txn_id has not been previously processed
                checkIfTransactionHasAlreadyBeenProcessed($paypalTxId);
                
                // process the order
                processOrder();
            } else {
                throw new \Exception('Oops! There was an unexpected error trying to verify your transaction.');
            }
        }
        fclose ($fp);
    }

    /**
     * Adds payment to payment table
     *
     * @param array $data Paypal Payment data
     * @return void
     */
    public function addPayment(array $data): void {
        try {
            $today = date('Y-m-d H:i:s');

            file_put_contents("log.txt", "---ADD_PAYMENT: " . print_r( 
                [$this->user_id,
                $data['txn_id'],
                $data['payer_id'],
                $data['subscr_id'],
                $data['mc_gross'],
                $data['payer_status'],
                $today], true ), 
            FILE_APPEND );

            $sql = "INSERT INTO `{$this->table}` (`user_id`, `txn_id`, `payer_id`, `subscription_id`, `amount`, `status`, `date_created`) 
                    VALUES(?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id,
                            $data['txn_id'],
                            $data['payer_id'],
                            $data['subscr_id'],
                            $data['mc_gross'],
                            $data['payer_status'],
                            $today]);

            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to add payment record. No rows added.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to add payment record.');
        } finally {
            $stmt = null;
        }
    } // end addPayment()

    /**
     * Checks if transaction was already added to payments table
     *
     * @param int $txn_id Paypal transaction id
     * @return boolean
     */
    public function checkTxnId(string $txn_id): bool {
        $sql = "SELECT COUNT(*) AS `exists` FROM `{$this->table}` WHERE `txn_id`=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$txn_id]);
        $row = $stmt->fetch();
        
        return $row['exists'] == 0;
    } // end checkTxnId()

    /**
     * Get the value of url
     * @return string
     */ 
    public function getUrl(): string
    {
        return $this->url;
    }
}



?>