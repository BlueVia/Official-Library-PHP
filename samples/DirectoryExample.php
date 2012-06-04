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

// The Directory constructor requires the application consumer key, consumer secret, 
// token and token secret
$consumerKey = "vw12012654505986"; //CONSUMER_KEY,
$consumerSecret = "WpOl66570544"; //CONSUMER_SECRET

$token_access = "ad3f0f598ffbc660fbad9035122eae74"; //TOKEN,
$token_secret = "4340b28da39ec36acb4a205d3955a853"; //TOKEN_SECRET,

try {

	// Get the user access info

	$directory= new BV_Directory($mode,$consumerKey,$consumerSecret,$token_access,$token_secret);
	$response=$directory->getAccessInfo();

	// Show the returned information, only if DEBUG is set to true

	if (DEBUG && $response) {
		print "<h1>Directory API</h1>";


		print "<h3>Access Info</h3>";
		print "<table border=\"1\">";
		foreach($response as $key2 =>$value2){
			print "<tr><td>$key2</td><td>".$value2."</td></tr>";
		}
		print "</table>";

	}

} catch(Exception $e) {
	print "<h3>User Access Info</h3>";
	print "<p>Exception retrieving user access info ".$e->getMessage()."</p>";
	print "<table border=\"1\">";
}

unset($directory);

