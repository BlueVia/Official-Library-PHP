<?php

### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',true);

### Set the country shortcode to get the messages
define('SHORTCODE',5698764);

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

$application_context_2legged = array(
    'app' => array(
      'consumer_key' => 'vw12012654505986', 
      'consumer_secret' => 'WpOl66570544' 
)
);

$bv = new BlueviaClient($mode,$application_context_2legged);
if ($bv)
{
	$smsMO = $bv->getService('SmsMO');
	if ($smsMO)
	{

		try {
			print "<table border=\"1\">";

			// Get received messages

			$received_messages = $smsMO->getMessages(SHORTCODE,true);

			// Show the returned information, only if DEBUG is set to true

			if(DEBUG) {
				print "<h1>Sms MO API</h1>";
				print "<h3>Received Messages</h3>";
				if ($received_messages){
					$received_messages=json_decode($received_messages);
					$received_messages=$received_messages->receivedSMS->receivedSMS;

					foreach ($received_messages as $key => $value){
						$number=$key+1;
						print "<tr><td>Message ".$number."</td></tr>";
						print "<tr><td>Text Message</td><td>".$value->message."</td></tr>";
						print "<tr><td>Origin address</td><td>".$value->originAddress->phoneNumber."</td></tr>";
						print "<tr><td>Destination address</td><td>".$value->destinationAddress->phoneNumber."</td></tr>";
						print "<tr><td>Date-time</td><td>".$value->dateTime."</td></tr>";
					}
				}

			}
		} catch(Exception $e) {
			print "<h1>Sms MO</h1>";
			print "<h3>Received Messages</h3>";
			print "<p>Exception retrieving SMS ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		unset($smsMO);
	}
}
