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

require_once '../includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'includes/checklogin.php'; // loads User class & checks if user is logged in
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\Paypal; 

$html_array = [];
$error = !isset($_GET['tx']) || empty($_GET['tx']) || $_GET['tx'] == NULL;

try {
    if ($error) {
        throw new \Exception('Your payment could not be processed due to a validation error');
    }

    $tx = $_GET['tx'];
    $paypal = new Paypal($pdo, $user->getId(), PAYPAL_SANDBOX);
    $res = $paypal->verifyTransactionPDT($tx);

    $html_array = [ 'h1_img_class' => 'far fa-check-circle', 
                    'h1_msg'   => 'Paypal payment successful',
                    'h1_class' => 'text-success',
                    'h5_msg'   => 'Thank you for your purchase'];
    
    if ($paypal->checkTxnId($tx)) {
        $paypal->addPDTPayment($_GET);
        $user->upgradeToPremiumPDT($_GET);
    } else {
        throw new \Exception ('Transaction was already processed.');
    }
} catch (\Exception $e) {
    $error = true;
    $html_array = [ 'h1_img_class' => 'fas fa-exclamation-circle',
                    'h1_msg'   => 'Paypal payment error',
                    'h1_class' => 'text-danger',
                    'h5_msg'   => $e->getMessage()];
}

?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex flex-row justify-content-center">
                <img class="img-fluid" style="max-width: 150px;" src="img/logo.svg" alt="Aprelendo logo">
            </div>
            <br>

            <h1 class="<?php echo $html_array['h1_class']; ?> text-center"><i
                    class="<?php echo $html_array['h1_img_class']; ?>"></i> <?php echo $html_array['h1_msg']; ?></h1>
            <h5 class="text-center"><?php echo $html_array['h5_msg']; ?></h5>
            <hr>
            <?php if (!$error): ?>
            <p>Below you will find your purchase details:</p>
            <table class="table borderless">
                <tbody>
                    <tr>
                        <th scope="row">Item:</th>
                        <td><?php echo $res['item_name']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Transaction Id:</th>
                        <td><?php echo $res['txn_id']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Amount:</th>
                        <td><?php echo $res['mc_currency'] . ' ' . $res['mc_gross'];  ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Status:</th>
                        <td><?php echo $res['payment_status']; ?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>
            <div class="text-center">
                <a href="/" class="btn btn-primary btn-lg" role="button">Go back to Home page</a>
            </div>
        </div>

    </div>
</div>

<?php require_once 'footer.php'?>