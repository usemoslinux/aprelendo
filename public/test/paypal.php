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

<form name="form-monthly-subscription" action="/payment.php" method="post" target="_top">
    <input type="hidden" name="cmd" value="_xclick-subscriptions">
    <input type="hidden" name="lc" value="US">
    <input type="hidden" name = "item_name" value = "Aprelendo - Subscription">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="src" value="1">
    <input type="hidden" name="a3" value="10">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="t3" value="M">
    <input type="hidden" name="p3" value="1">
    <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribeCC_LG.gif:NonHostedGuest">
    <input type="submit" name="submit" value="Subscribe"/>
</form>



</body>
</html>