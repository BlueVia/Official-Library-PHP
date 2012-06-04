<?php

// Set DEBUG true to see the response content
define('DEBUG',true);

// Set your pear and php local path. 
// You must edit this example and complete it with the path in your file system.
$oldpath = set_include_path('/usr/local/share/pear:/usr/share/php:.');

// The path shown above is only for Unix users. If you are using Windows comment the line
// and include this one instead.
// $oldpath = set_include_path('.;C:\php\pear');

// Set your Includes.php path
include_once "../sdk/Includes.php";


// BlueVia provides three environments to support the different development stages of your app.
// Sandbox for testing. Test Live for accessing the live network.
// Note that test mode is free of charge, you only need  SMS and MMS API credits.
// You can choose which of them to use depending on the API endpoint you need.
$mode=BV_Mode::SANDBOX;

// The Payment constructor when youre application is going to make a payment
// requires the application consumer key, consumer secret, token and token secret.
$consumerKey = "XXXXXXXXXXXXXX"; 
$consumerSecret = "YYYYYYYYYYYYY"; 
$token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$tokenSecret='YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';



try{
	// The First step for using the Location Api, is to create the Oauth object
	$payment= new BV_Payment($mode,$consumerKey,$consumerSecret,$token,$tokenSecret);


	print "<table border=\"1\">";

	// Make the payment
	$paymentResponse= $payment->payment(0,'EUR');

	// Show the returned information, only if DEBUG is set to true
	if (DEBUG && $paymentResponse) {
		print "<h1>PAYMENT API</h1>";
		print "<h3>Payment method</h3>";
		print "<table border=\"1\">";
		foreach($paymentResponse as $key => $value){
			print "<tr><td>$key</td><td>$value</td></tr>";
		}
		print "</table>";
	}
} catch(Exception $e) {
	print "<h1>Payment ERROR</h1>";
	print "<h3>Payment method</h3>";
	print "<p>Exception ". $e->getMessage(). "</p>";
}
try{
	// Get the transaction Id from the payment response
	$transactionId=$paymentResponse->transactionId;

	// Get the payment Status
	$paymentStatus=$payment->getPaymentStatus($transactionId);

	// Show the returned information, only if DEBUG is set to true
	if (DEBUG && $paymentResponse) {
		print "<h3>Get Payment Status method</h3>";
		print "<table border=\"1\">";
		foreach($paymentStatus as $key => $value){
			print "<tr><td>$key</td><td>$value</td></tr>";
		}
		print "</table>";
	}
} catch(Exception $e) {
	print "<h1>Payment ERROR</h1>";
	print "<h3>Get Payment Status method</h3>";
	print "<p>Exception". $e->getMessage(). "</p>";
}
try{
	// Cancel payment authorization
	$response=$payment->cancelAuthorization();

	if (DEBUG && $response) {
		print "<h3>Cancel Authorization method</h3>";
		print "Your payment authorization has been cancelled succesfully.";
	}
} catch(Exception $e) {
	print "<h1>Payment ERROR</h1>";
	print "<h3>Cancel authorization</h3>";
	print "<p>Exception ". $e->getMessage(). "</p>";
}





