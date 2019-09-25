<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paypal Integration Test</title>
</head>
<body>

    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="9DZB8M2T6979N">
<table>
<tr><td><input type="hidden" name="on0" value="Payment options">Payment options</td></tr><tr><td><select name="os0">
	<option value="Monthly">Monthly : $10,00 USD - mensual</option>
	<option value="Yearly">Yearly : $100,00 USD - anual</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.sandbox.paypal.com/es_XC/i/scr/pixel.gif" width="1" height="1">
</form>

IPN testing

<form name="form-monthly-subscription" action="/payment.php" method="post" target="_top">
    <input type="hidden" name="cmd" value="_xclick-subscriptions">
    <input type="hidden" name="lc" value="US">
    <input type="hidden" name = "item_name" value = "Monthly Subscription">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="src" value="1">
    <input type="hidden" name="a3" value="10">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="t3" value="M">
    <input type="hidden" name="p3" value="1">
    <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
    <input type="submit" name="submit" value="Subscribe"/>
</form>


IPN response testing

<form name="form-monthly-subscription-response" action="/payment.php" method="post" target="_top">
        <!-- <input type="hidden" name="item_number" value="1"> -->
        <input type="hidden" name="item_name" value="Monthly Subscription">
        <input type="hidden" name="payment_status" value="Completed">
        <input type="hidden" name="mc_gross" value="10.00">
        <input type="hidden" name="mc_currency" value="USD">
        <input type="hidden" name="txn_id" value="6A304059917411228">
        <input type="hidden" name="receiver_email" value="sb-fikun215027%40business.example.com">
        <input type="hidden" name="payer_email" value="sb-znule252443%40personal.example.com">
        <input type="hidden" name="custom" value="">
        <input type="submit" name="submit" value="Get response"/>
</form>

<!-- 
'item_name'         =>  $_POST['item_name'],
        'item_number'       =>  $_POST['item_number'],
        'payment_status'    =>  $_POST['payment_status'],
        'payment_amount'    =>  $_POST['mc_gross'],
        'payment_currency'  =>  $_POST['mc_currency'],
        'txn_id'            =>  $_POST['txn_id'],
        'receiver_email'    =>  $_POST['receiver_email'],
        'payer_email'       =>  $_POST['payer_email'],
        'custom'            =>  $_POST['custom'],


        address_city=San+Jose
address_country_code=US
address_country=United+States
address_name=Test+User
address_state=CA
address_status=confirmed
address_street=1+Main+St
address_zip=95131
charset=windows-1252
custom=
first_name=Test
handling_amount=0.00
item_name=
item_number=
last_name=User
mc_currency=USD
mc_fee=0.88
mc_gross=19.95
notify_version=2.6
payer_email=gpmac_1231902590_per%40paypal.com
payer_id=LPLWNMTBWMFAY
payer_status=verified
payment_date=20%3A12%3A59+Jan+13%2C+2009+PST
payment_fee=0.88
payment_gross=19.95
payment_status=Completed
payment_type=instant
protection_eligibility=Eligible
quantity=1
receiver_email=gpmac_1231902686_biz%40paypal.com
receiver_id=S8XGHLYDW9T3S
residence_country=US
shipping=0.00
tax=0.00
test_ipn=1
transaction_subject=
txn_id=61E67681CH3238416
txn_type=express_checkout
verify_sign=AtkOfCXbDm2hu0ZELryHFjY-Vb7PAUvS6nMXgysbElEn9v-1XcmSoGtf -->


</body>
</html>