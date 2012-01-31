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
      'consumer_key' => 'vw12012654505986', 
      'consumer_secret' => 'WpOl66570544' 
),
	'user' => array(
      'token_access' => 'ad3f0f598ffbc660fbad9035122eae74', 
      'token_secret' => '4340b28da39ec36acb4a205d3955a853' 
)
);

$bv = new BlueviaClient($mode,$application_context);
if ($bv)
{
	$advertising=$bv->getService('Advertising');

	try {
		print "<table border=\"1\">";
		
					
		// Get your advertising

		$response = $advertising->request(
			array(
				'user_agent' => 'Mozilla 5.0', // User agent of the mobile device where the ad
												  // is going to be shown
			
				'ad_space' => 'BV15125',		  // The ad_space value you got when you request your 
												  // Advertising API
			
				'protection_policy' => '1', 	  // The adult control policy.

				'country' => 'AR' 				  // Country where the user using your application 
												  // is located.
			)
		);

		// Show the returned information, only if DEBUG is set to true

		if($response && DEBUG) {
			print "<h1>Advertising</h1>";
			print "<p><a href=\"".$response[0]["interaction"]."\"><img src=\"".$response[0]["value"]."\"></a></p>";
		}

	} catch(Exception $e) {
		print "<h1>Advertising API</h1>";
		print "<p> Exception: ".$e->getMessage()."</p>";
	}

	// Show the http request and reply , only if DEBUG is set to true
	
	if (DEBUG){
		print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
		print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
		print "</table>";
	}
	
	unset($advertising);
}