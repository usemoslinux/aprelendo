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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // check if logged in and set $user

// For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = true;

// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
    'email' => 'sb-fikun215027@business.example.com',
    'return_url' => 'https://www.aprelendo.com/payment-successful.html',
    'cancel_url' => 'https://www.aprelendo.com/payment-cancelled.html',
    'notify_url' => 'https://www.aprelendo.com/payment.php'
];

$paypal = new Paypal($con, $user->id, $enableSandbox);

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {
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
    // and currency so that these aren't overridden by the form data.
    $data['a3'] = $_POST["t3"] === 'Y' ? '99' : '10'; // amount
    $data['currency_code'] = 'USD'; // currency

    // Add any custom fields for the query string.
    //$data['custom'] = USERID;

    // Build the query string from the data.
    $queryString = http_build_query($data);

    // Redirect to paypal IPN
    header('location:' . $paypal->url . '?' . $queryString);
    exit;

} else {
    // Handle the PayPal response.

    // Assign posted variables to local data array.
    $data = [
        'item_name'         =>  $_POST['item_name'],
        // 'item_number'       =>  $_POST['item_number'],
        'payment_status'    =>  $_POST['payment_status'],
        'payment_amount'    =>  $_POST['mc_gross'],
        'payment_currency'  =>  $_POST['mc_currency'],
        'txn_id'            =>  $_POST['txn_id'],
        'receiver_email'    =>  $_POST['receiver_email'],
        'payer_email'       =>  $_POST['payer_email'],
        'custom'            =>  $_POST['custom'],
    ];

    // We need to verify the transaction comes from PayPal and check we've not
    // already processed the transaction before adding the payment to our
    // database.
    if (!$paypal->verifyTransaction($_POST) && $paypal->checkTxnid($data['txn_id'])) {
        if ($paypal->addPayment($data)) {
            // Payment successfully added.
        }
    }
}

?>