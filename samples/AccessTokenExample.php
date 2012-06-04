<?php

// Set DEBUG true to see the response content
define('DEBUG',true);

// Set your pear and php local path. 
// You must edit this example and complete it with the path in your file system.
$oldpath = set_include_path('/usr/local/share/pear:/usr/share/php:.');

// The path shown above is only for Unix users. If you are using Windows comment the line
// and include this one instead.
// $oldpath = set_include_path('.;C:\php\pear');

// Set your BlueviaClient.php path
include_once "../sdk/Includes.php";

// BlueVia provides three environments to support the different development stages of your app.
// Sandbox for testing. Test Live for accessing the live network.
// Note that test mode is free of charge, you only need  SMS and MMS API credits.
// You can choose which of them to use depending on the API endpoint you need.

$mode=BV_Mode::SANDBOX;

// The Oauth constructor requires the application consumer key and consumer secret.

$consumerKey = "XXXXXXXXXXXX";
$consumerSecret = "YYYYYYYYYYYYY";

// The First step for using the Oauth Api, is to create the Oauth object

$oauth = new BV_OAuth($mode,$consumerKey,$consumerSecret);


if($oauth){
	try{

		// Set the oauth verifier obtained at Bluevia Connect when the user authorizes the application
		$oauthVerifier='xxxxx';

		// Set the request token obtained when you call the request token function.
		// You can learn how to get the request tokens in RequestTokenExample.php
		$requestToken='XXXXXXXXXXXXXXXXXXXXXXXXXXXX';
		$requestSecret='YYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

		// Get Access Tokens

		$access_token = $oauth->getAccessToken($oauthVerifier,$requestToken, $requestSecret);

		// Show the returned information, only if DEBUG is set to true

		if(DEBUG && $access_token){
			print "<h1>Oauth API</h1>";
			print "<h3>Access token</h3>";
			print "<table border=\"1\">";
			echo "<tr><td>Access Token</td><td>".$access_token->key."</td>";
			echo "<tr><td>Access Token Secret</td><td>".$access_token->secret."</td></table>";
		}
	} catch(Exception $e) {
		print "<h1>Oauth API</h1>";
		echo "<br><br><table><th>Access Token</th>";
		echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
	}
}

