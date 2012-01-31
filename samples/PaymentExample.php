<?php
### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',true);


### Set your Zend Framework, pear and php local path
$oldpath = set_include_path('.:/usr/local/zend/share/ZendFramework/library:/usr/local/zend/share/pear:/usr/share/php');
### Set your BlueviaClient.php path
include_once "../src/BlueviaClient.php";

// BlueVia provides three environments to support the different development stages of your app.
// Sandbox for testing. Test Live for accessing the live network.
// Note that test mode is free of charge, you only need  SMS and MMS API credits.
// You can choose which of them to use depending on the API endpoint you need.
$mode= BlueviaClient_Api_Constants::SANDBOX_MODE;

// PHP SDK wraps any request to BlueVia API's by using a generic object BlueviaClient.
// This object uses the Component Pattern to fetch any service required by the developer (oAuth, SMS, Directory or Advertising).
// The BlueviaClient constructor requires an array containing the application consumer key, consumer secret and the access token data

$application_context = array(
    'app' => array(
      'consumer_key' => 'XXXXXXXXXXX', 
      'consumer_secret' => 'YYYYYYYYYY'       
),
	'user' => array(
      'token_access' => 'XXXXXXXXXXXXXXXXXXXXX', 
      'token_secret' => 'YYYYYYYYYYYYYYYYYYYYY' 
)
);
$bv = new BlueviaClient($mode,$application_context);
if ($bv)
{

	$payment = $bv->getService('Payment');

	if($payment) {
		echo "<br><br><table border=\"1\">";
		try {

			// Make a payment

			$payment_result = $payment->payment("177",	// Amount to be charged.

												"EUR"	// Type of currency which corresponds 
														// with the amount above.
			);
		} catch (Exception $ex) {
			print "<h1>Payment API</h1>";
			print "<h3>Payment</h3>";
			print "Payment exception: ". $ex->getMessage();
		}

		// Show the exception message

		if (!empty($payment_result['type'])){
			print "<h1>Payment API</h1>";
			print "<h3>Payment</h3>";
			print "Payment exception: ". $payment_result['v1:message'];
		}
			
		// Show the http request, reply and content , only if DEBUG is set to true
			
		if (DEBUG) {
			if (!empty($payment_result['transactionId'])){
				print "<h1>Payment API</h1>";
				print "<h3>Payment</h3>";
				print "<tr><td>Transaction ID</td><td>".$payment_result['transactionId']."</td></tr>";
				print "<tr><td>Transaction status</td><td>".$payment_result['transactionStatus']."</td></tr>";
				print "<tr><td>Transaction status description</td><td>".$payment_result['transactionStatusDescription']."</td></tr>";
			}
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		try {

			// Get payment status

			$payment_status = $payment->getPaymentStatus($payment_result['transactionId']);

		} catch (Exception $ex) {
			print "<h3>Get payment status</h3>";
			print "Get payment status exception: " .$ex->getMessage();
		}
		
		// Show the http request, reply and content , only if DEBUG is set to true
		
		if (DEBUG) {
			echo "<br><br><table border=\"1\">";
			print "<h3>Get payment status</h3>";
			if ($payment_status){
				print "<tr><td>Transaction status</td><td>".$payment_status['transactionStatus']."</td></tr>";
				print "<tr><td>Transaction status description</td><td>".$payment_status['transactionStatusDescription']."</td></tr>";
			}
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		try {
			
			// Cancel payment authorization
			
			$payment->cancelAuthorization();
			
		} catch (Exception $ex) {
			print "<h3>Cancel payment authorization</h3>";
			print "Cancel payment authorization: " .$ex->getMessage();
		}
		
		// Show the http request, reply and content , only if DEBUG is set to true
		
		if (DEBUG) {
			print "<h3>Cancel payment authorization</h3>";
			echo "<br><br><table border=\"1\">";
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

	}
}
