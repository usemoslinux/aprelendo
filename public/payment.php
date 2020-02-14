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

require_once '../includes/dbinit.php'; // connect to database

$no_redirect = TRUE; // used by checklogin.php; otherwise, would redirect to login page

require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user

// For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = true;

// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
    'email' => 'sb-fikun215027@business.example.com',
    'return_url' => 'https://www.aprelendo.com/paymentvalidation.php',
    'cancel_url' => 'https://www.aprelendo.com/paymentcancelled.php',
    'notify_url' => 'https://www.aprelendo.com/payment.php'
];

try {
    if (empty($_POST)) {
        throw new \Exception ("No IPN post information");
    }

    // Check if paypal request or response
    if (isset($_POST["cmd"]) && $_POST["cmd"] === '_xclick') {
        // It's a request

        // Grab the post data so that we can set up the query string for PayPal.
        // Ideally we'd use a whitelist here to check nothing is being injected into
        // our post data.
        $data = [];
        foreach ($_POST as $key => $value) {
            $data[$key] = stripslashes($value);
        }

        // Set the PayPal account.
        $data['business'] = $paypalConfig['email'];

        // Set the PayPal return addresses.
        $data['return'] = stripslashes($paypalConfig['return_url']);
        $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
        $data['notify_url'] = stripslashes($paypalConfig['notify_url']);

        // Set the details about the product being purchased, including the amount
        // and currency so that these override form data.
        switch ($data['item_number']) {
            case 1:
                // 1 month pass
                $data['item_name'] = 'Aprelendo - 1 Month Pass';
                $data['amount'] = '10.00';
                break;
            case 2:
                $data['item_name'] = 'Aprelendo - 3 Months Pass';
                $data['amount'] = '25.00';
                break;
            case 3:
                $data['item_name'] = 'Aprelendo - 6 Months Pass';
                $data['amount'] = '50.00';
                break;
            case 4:
                $data['item_name'] = 'Aprelendo - 1 Year Pass';
                $data['amount'] = '99.00';
                break;
            default:
        }
        
        $data['currency_code'] = 'USD'; // currency
        $data['no_shipping'] = '2';
        $data['no_note'] = '1';
        $data['tax'] = '0';

        // Add user id
        $data['custom'] = $user->getId();

        // Build the query string from the data.
        $queryString = http_build_query($data);

        // Redirect to paypal IPN
        $paypal = new Paypal($pdo, $user->getId(), PAYPAL_SANDBOX);
        header('location:' . $paypal->getUrl() . '?' . $queryString);
        exit;

    } else {
        // Handle the PayPal IPN response.
        
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $post_array = [];
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
            $post_array[$keyval[0]] = urldecode($keyval[1]);
        }

        if (!is_array($post_array) || empty($post_array)) {
            throw new \Exception('There was an unexpected error in the payment information provided.');
        }

        // We need to verify the transaction comes from PayPal and check we've not
        // already processed the transaction before adding the payment to our
        // database.
        $paypal = new Paypal($pdo, $post_array['custom'], PAYPAL_SANDBOX);
        
        if (isset($post_array['txn_id']) && $paypal->verifyTransactionIPN($post_array) && $paypal->checkTxnId($post_array['txn_id'])) {
            $paypal->addIPNPayment($post_array);
            $user->upgradeToPremiumIPN($post_array);
        } else {
            if (!isset($post_array['amount3'])) {
                throw new \Exception ('Transaction could not be verified. Payment Id might be wrong.');
            }
        }
    }
} catch (\Exception $e) {
    file_put_contents("paypal.log", $e->getMessage() . "\n\n----post_array: " . print_r( $post_array, true ), FILE_APPEND );
}

?>