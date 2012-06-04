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

$mode=BVMode::SANDBOX;

// The Oauth constructor requires the application consumer key and consumer secret.

$consumerKey = "vw12012654505986"; 
$consumerSecret = "WpOl66570544"; 

// The First step for using the Oauth Api, is to create the Oauth object

$oauth = new BV_OAuth($mode,$consumerKey,$consumerSecret);


if($oauth){
	try{
			
		// Request token. The callback parameter is set to null, this means you are going to
		// use the OutOfBound authorization. You can learn the three authorization ways
		// at https://bluevia.com/en/knowledge/libraries.PHP.oauth
			
		$request_token = $oauth->getRequestTokenSmsHandshake('44123456789');
			
		// Show the returned information, only if DEBUG is set to true
			
		if(DEBUG && $request_token) {
			print "<h1>Oauth API</h1>";
			print "<h3>Request token</h3>";
			print "<table border=\"1\">";
			print "<tr><td>Request Token</td><td>".$request_token->key."</td></tr>";
			print "<tr><td>Request Token Secret</td><td>".$request_token->secret."</td></tr>";
			print "</table>";
		}

	} catch(Exception $e) {
		print "<h1>Oauth API</h1>";
		echo "<br><br><table><th>Request Token</th>";
		echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
	}
}
