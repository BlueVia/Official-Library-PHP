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

// The Oauth constructor requires the application consumer key and consumer secret.
$consumerKey = "XXXXXXXXXXX"; //CONSUMER_KEY,
$consumerSecret = "YYYYYYYYYYYY"; //CONSUMER_SECRET

// The First step for using the Oauth Api, is to create the Oauth object
$oauth = new BV_OAuth($mode,$consumerKey,$consumerSecret);


if($oauth){

	if (empty($_GET['oauth_verifier'])){
		try {
			// Set the callback URL, where you want to recieve the Oauth verifier.
			if (isset($_GET['original_url'])){
				$callbackUrl=$_GET['original_url'];
			}

			// Request token. Callback parameter is a defined callback URL. This means you are
			// going to use the WebOauth authorization. You can learn the three authorization
			// ways at https://bluevia.com/en/knowledge/libraries.PHP.oauth

			$request_token = $oauth->getRequestToken($callbackUrl);
		} catch(Exception $e) {
			print "<h1>Oauth API</h1>";
			echo "<br><br><table><th>Request Token</th>";
			echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
		}
	}else{
		try {

			// Get the Access Token

			$access_token = $oauth->getAccessToken($_GET['oauth_verifier']);

			// Show the returned information, only if DEBUG is set to true

			if (DEBUG){
				print "<h1>Oauth API</h1>";
				echo "<br><br><table border=\"1\">";
				echo "<tr><td>Consumer Key</td><td>".$consumerKey."</td>";
				echo "<tr><td>Consumer Secret</td><td>".$consumerSecret."</td>";
				echo "<tr><td>Oauth verifier</td><td>".$_GET['oauth_verifier']."</td>";
				echo "<tr><td>Access Token Key</td><td>".$access_token->key."</td>";
				echo "<tr><td>Access Token Secret</td><td>".$access_token->secret."</td></table>";
			}

		} catch(Exception $e) {
			echo "<br><br><table><th>Access Token</th>";
			echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
		}



	}
	unset($oauth);
}