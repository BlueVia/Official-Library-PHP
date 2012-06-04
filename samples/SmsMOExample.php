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

// The Mms MT constructor requires the application consumer key, consumer secret, token
// and token secret. The Mms MO only needs the consumer key and secret.

$consumerKey = "vw12012654505986"; //CONSUMER_KEY,
$consumerSecret = "WpOl66570544"; //CONSUMER_SECRET

$tokenAccess = "ad3f0f598ffbc660fbad9035122eae74"; //TOKEN,
$tokenSecret = "4340b28da39ec36acb4a205d3955a853"; //TOKEN_SECRET,

try{

	$smsMT= new BV_MTSms($mode,$consumerKey,$consumerSecret,$tokenAccess,$tokenSecret);

	print "<table border=\"1\">";



	// Set the shortcode where the sms will be sent
	// Sending sms to a shortcode is only available on sandbox mode.
	$shortcode='5698765';

	// To check SMS sent to your application following a polling strategy the first word
	// in your sms text must be your application keyword (SANDBLUEDEMOS).
	$text='SANDBLUEDEMOS Your message text goes here.';

	// Send SMS
	$messageId = $smsMT->send($shortcode,$text);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Sms MT API</h1>";
		print "<h3>Send SMS</h3>";
		if($messageId) {
			print "<tr><td>Sms ID</td><td>".$messageId."</td></tr>";
		}
		print "</table>";
	}
} catch (Exception $e) {
	print "<h1>Sms MT</h1>";
	print "<h3>Send SMS</h3>";
	print "<p> Exception sending SMS: ".$e->getMessage()."</p>";
}


try {
	print "<table border=\"1\">";

	// Retrieve SMS delivery status
	$smsMO= new BV_MoSms($mode,$consumerKey,$consumerSecret);

	$smsInfo = $smsMO->getAllMessages($shortcode);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h3>Message info</h3>";
		if($smsInfo) {
			foreach($smsInfo as $id =>$status){
				print "<tr><td>Sms $id</td></tr>";
				foreach($status as $key=>$value){
					print "<tr><td>$key</td><td>".$value."</td></tr>";
				}
			}
			print "</table>";
		}
	}
} catch (Exception $e) {
	print "<h3>Delivery status</h3>";
	print "<p>Exception retreiving the SMS get all messages: ".$e->getMessage()."</p>";
}


unset($smsMT);
unset($smsMO);


?>



