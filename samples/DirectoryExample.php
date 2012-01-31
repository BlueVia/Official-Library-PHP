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
	$directory = $bv->getService('Directory');

	if($directory) {

		try {

			// Get the user profile

			$user_info = $directory->getUserInfo(BlueviaClient_Api_Directory::USER_PROFILE);

		} catch(Exception $e) {
			print "<h1>Directory API</h1>";
			print "<h3>User Profile</h3>";
			print "<p>Exception retrieving user profile ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<h3>User Profile</h3>";
			print "<table border=\"1\">";
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}


		try {

			// Get the user access info
			
			$user_info = $directory->getUserInfo(BlueviaClient_Api_Directory::USER_ACCESS_INFO);

			// Show the returned information, only if DEBUG is set to true

			if (DEBUG && $user_info) {
				print "<h3>User Access Info</h3>";
				print "<table border=\"1\">";
				print "<tr><td>APN</td><td>".$user_info->userAccessInfo->apn."</td></tr>";
				print "<tr><td>Roaming</td><td>".$user_info->userAccessInfo->roaming."</td></tr>";
			}

		} catch(Exception $e) {
			print "<h3>User Access Info</h3>";
			print "<p>Exception retrieving user access info ".$e->getMessage()."</p>";
			print "<table border=\"1\">";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		try {


			// Get the user terminal info

			$user_info = $directory->getUserInfo(BlueviaClient_Api_Directory::USER_TERMINAL_INFO);

		} catch(Exception $e) {
			
			print "<h3>User Terminal Info </h3>";
			print "<p>Exception retrieving user terminal info ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<h3>User Terminal Info </h3>";
			print "<table border=\"1\">";
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		unset($directory);
	}
}