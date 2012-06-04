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

// The Advertising constructor requires the application consumer key, consumer secret,
// token and token secret.

$consumerKey = "vw12012654505986";
$consumerSecret = "WpOl66570544";

$tokenAccess = "ad3f0f598ffbc660fbad9035122eae74";
$tokenSecret = "4340b28da39ec36acb4a205d3955a853";



try {
	print "<table border=\"1\">";

	// The First step for using the Oauth Api, is to create the Oauth object

	$advertising = new BV_Advertising($mode,$consumerKey,$consumerSecret, 
	$tokenAccess,$tokenSecret);

	// Get your image advertising

	$response=$advertising->getAdvertising('BVPoz15595','ImAdId',Ad_Presentation::IMAGE);

	// Show the returned information, only if DEBUG is set to true

	if($response && DEBUG) {
		print "<h1>Advertising API</h1>";
		print "<h3>Image advertising</h3>";
		print "<table border=\"1\">";
		print "<tr><td>Advertising ID</td><td>$response->id</td></tr>";
		print "</table>";
		print "<p><a href=\"".$response->creativeElement->interaction."\">";
		print "<img src=\"".$response->creativeElement->value."\" alt=\"Adv image\" /></a></p>";

	}

	// Get your text advertising

	$response=$advertising->getAdvertising('BVPoz15595','TxtAdId',Ad_Presentation::TEXT);
	
	// Show the returned information, only if DEBUG is set to true
	if($response && DEBUG) {
		print "<h3>Text advertising</h3>";
		print "<table border=\"1\">";
		print "<tr><td>Advertising ID</td><td>$response->id</td></tr>";
		print "</table>";
		print "<p><a href=\"".$response->creativeElement->interaction."\">";
		print $response->creativeElement->value."</a></p>";
	}

} catch(Exception $e) {
	print "<h1>Advertising API</h1>";
	print "<p> Exception: ".$e->getMessage()."</p>";
}

unset($advertising);
