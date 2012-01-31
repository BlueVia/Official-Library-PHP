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
	$location = $bv->getService('Location');
	if($location) {
		try {

			print "<table border=\"1\">";
			
			// Get the location
			
			$user_location = $location->getLocation();

			// Show the returned information, only if DEBUG is set to true

			if (DEBUG && $user_location) {

				$user_location=$user_location->terminalLocation;
				print "<h1>Location API</h1>";
				print "<h3>User Location </h3>";
				print "<tr><td>Located party</td><td>".$user_location->locatedParty->alias."</td></tr>";
				print "<tr><td>Report status</td><td>".$user_location->reportStatus."</td></tr>";
				print "<tr><td>Coordinates: Latitude</td><td>".$user_location->currentLocation->coordinates->latitude."</td></tr>";
				print "<tr><td>Coordinates: Longitude</td><td>".$user_location->currentLocation->coordinates->longitude."</td></tr>";
				print "<tr><td>Current location accuracy</td><td>".$user_location->currentLocation->accuracy."</td></tr>";
				print "<tr><td>Timestamp</td><td>".$user_location->currentLocation->timestamp."</td></tr>";
			}


		} catch(Exception $e) {
			print "<h1>Location</h1>";
			print "<h3>User Location </h3>";
			print "<p>Exception retrieving user location ". $e->getMessage(). "</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG) {
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}
	}
}

