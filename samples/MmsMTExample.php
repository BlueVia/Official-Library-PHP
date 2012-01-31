<?php

### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',false);

### Set the recepient of the MMS
define('MMS_RECIPIENT',56456456444);


### Set your Zend Framework, pear and php local path
$oldpath = set_include_path('.:/usr/local/zend/share/ZendFramework/library:/usr/local/zend/share/pear:/usr/share/php');
### Set your BlueviaClient.php path
include_once "../src/BlueviaClient.php";

// BlueVia provides three environments to support the different development stages of your app.
// Sandbox for testing. Test Live for accessing the live network.
// Note that test mode is free of charge, you only need MMS API credits.
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
	$mmsMT = $bv->getService('MmsMT');
	if ($mmsMT)
	{
		try{
			print "<table border=\"1\">";

			// Sets an address element containing the phone number to which this message is sent
			$mmsMT->addRecipient(MMS_RECIPIENT);
			// Sets a message element containing the message itself
			$mmsMT->setMessage('Your message goes here');
			// Sets a subject element containing the message's subject
			$mmsMT->setSubject("Message subject");
			// Sets a file element containing
			//$mmsMT->addFile("/path/to/image",BlueviaClient_Messaging_Mimetype::IMAGE_JPEG);

			// Send MMS

			$result = $mmsMT->send();

			// Show the returned information, only if DEBUG is set to true

			if (defined('DEBUG') && constant('DEBUG')) {
				print "<h1>Mms MT API</h1>";
				print "<h3>Send MMS</h3>";
				if($result) if($result) {
					print "<tr><td>Mms ID</td><td>".$result."</td></tr>";
				}
			}
		} catch (Exception $e) {
			print "<h1>Mms MT</h1>";
			print "<h3>Send MMS</h3>";
			print "<p> Exception sending MMS: ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}


		try {
			print "<table border=\"1\">";

			// Retrieve MMS delivery status

			$delivery_status = $mmsMT->getDeliveryStatus($result);

			// Show the returned information, only if DEBUG is set to true
			
			if (defined('DEBUG') && constant('DEBUG')) {
				print "<h3>Delivery status</h3>";
				if ($delivery_status){
					print "<tr><td>Phone number</td><td>".$delivery_status->messageDeliveryStatus->messageDeliveryStatus[0]->address->phoneNumber."</td></tr>";
					print "<tr><td>Delivery status</td><td>".$delivery_status->messageDeliveryStatus->messageDeliveryStatus[0]->deliveryStatus."</td></tr>";
				}
			}
		} catch (Exception $e) {
			print "<h3>Delivery status</h3>";
			print "<p>Exception retreiving the MMS delivery status: ".$e->getMessage()."</p>";
		}
		
		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}
	}
	unset($mmsMT);
}
?>