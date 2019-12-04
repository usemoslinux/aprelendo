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
    <input type="hidden" name = "item_name" value = "Aprelendo - Monthly Subscription">
    <input type="hidden" name = "item_number" value = "1">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="src" value="1">
    <input type="hidden" name="sra" value="1">
    <input type="hidden" name="a3" value="10.00">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="t3" value="M">
    <input type="hidden" name="p3" value="1">
    <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
    <input type="submit" name="submit" value="Subscribe"/>
</form>

<!-- 
a3 (Required) Regular rate. This is the price of the subscription
p3 (Required) Regular billing cycle. This is the length of the billing cycle. The number is modified by the
    regular billing cycle units (t3, below)
t3 (Required) Regular billing cycle units. This is the units of the regular billing cycle (p3, above) 
    Acceptable values are: D (days), W (weeks), M (months), Y (years)
src (Optional) Recurring payments. If set to “1,” the payment will recur unless your customer cancels the 
    subscription before the end of the billing cycle. If omitted, the subscription payment will not recur 
    at the end of the billing cycle 
-->


IPN response testing

<form name="form-monthly-subscription-response" action="/payment.php" method="post" target="_top">
        <input type="hidden" name="item_number" value="1">
        <input type="hidden" name="item_name" value="Aprelendo - Monthly Subscription">
        <input type="hidden" name="payment_status" value="Completed">
        <input type="hidden" name="mc_gross" value="10.00">
        <input type="hidden" name="mc_currency" value="USD">
        <input type="hidden" name="txn_id" value="6A304059917411228">
        <input type="hidden" name="receiver_email" value="sb-fikun215027%40business.example.com">
        <input type="hidden" name="payer_email" value="sb-znule252443%40personal.example.com">
        <input type="hidden" name="custom" value="">
        <input type="submit" name="submit" value="Get response"/>
</form>

</body>
</html>