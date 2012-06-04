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

// The SmsMO constructor requires the application consumer key and consumer secret.

$consumerKey = "XXXXXXXXXXX"; //CONSUMER_KEY,
$consumerSecret = "YYYYYYYYYY"; //CONSUMER_SECRET


try{

	$smsMO= new BV_MOSms($mode,$consumerKey,$consumerSecret);

	print "<table border=\"1\">";

	// First of all set the required parameters:
	
	// Element containing the short number corresponding to 
	// the user's country, including the country code.
	$shortcode = 'XXXXX'; 

	// Element with the URI where your application is expecting to receive
	// the SMS notifications. Note that BlueVia only accepts HTTPS endpoints
	// and the server certificate must be provided during the process of
	// requesting the API key.
	$endpoint = 'https://YYYYYYYYYY';

	// Element containing the keyword you chose for your application when you
	// requested the API key. Note that keywords are case-insensitive.
	$criteria = 'YOUR_KEYWORD';

		
	$notificationID = $smsMO->startNotification($shortcode,$endpoint,$criteria);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Sms MO API</h1>";
		print "<h3>Start SMS Notifications</h3>";
		if($notificationID) {
			print "<tr><td>Notification ID</td><td>".$notificationID."</td></tr>";
		}
		print "</table>";
	}
} catch (Exception $e) {
	print "<h1>Sms MT</h1>";
	print "<h3>Start SMS Notifications</h3>";
	print "<p> Exception starting SMS notifications: ".$e->getMessage()."</p>";
}


try {
	print "<table border=\"1\">";

	$stopNotification=$smsMO->stopNotification($notificationID);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h3>Stop SMS Notifications</h3>";
		if($stopNotification) {
			print "<p>You have stopped you notification service succesfully!!</p>";
			print "</table>";
		}
	}
} catch (Exception $e) {
	print "<h3>Delivery status</h3>";
	print "<p>Exception stoping SMS notifications: ".$e->getMessage()."</p>";
}


unset($smsMT);
unset($smsMO);


?>



