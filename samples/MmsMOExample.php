<?php
### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',true);

### Set the shortcode to recieve MMS
define('SHORTCODE',5698764);


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

$application_context_2legged = array(
    'app' => array(
      'consumer_key' => 'vw12012654505986', 
      'consumer_secret' => 'WpOl66570544' 
)
);

$bv = new BlueviaClient($mode,$application_context_2legged);
if ($bv)
{
	$mmsMO = $bv->getService('MmsMO');
	if ($mmsMO)
	{

		try{
			print "<table border=\"1\">";

			// Recieve all MMS

			$received_messages = $mmsMO->getMessages(SHORTCODE,true);
			$received_messages=json_decode($received_messages);
			$received_messages=$received_messages->receivedMessages->receivedMessages;
			$message_identifier=$received_messages -> messageIdentifier;
			$attachmentURL= $received_messages -> attachmentURL;

			// Show the returned information, only if DEBUG is set to true

			if (defined('DEBUG') && constant('DEBUG')) {
				print "<h1>Mms MO API</h1>";
				print "<h3>Received Messages</h3>";
				if($received_messages) {
					print "<tr><td>Message identifier</td><td>".$received_messages->messageIdentifier."</td></tr>";
					print "<tr><td>Origin address</td><td>".$received_messages->originAddress->phoneNumber."</td></tr>";
					print "<tr><td>Destination address</td><td>".$received_messages->destinationAddress->phoneNumber."</td></tr>";
					print "<tr><td>Date-time</td><td>".$received_messages->dateTime."</td></tr>";
					print "<tr><td>Subject</td><td>".$received_messages->subject."</td></tr>";
					foreach($attachmentURL as $key => $value){
						print "<tr><td>ID</td><td>".$value->href."</td></tr>";
						print "<tr><td>Content Type</td><td>".$value->contentType."</td></tr>";
					}
				}
			}

		} catch(Exception $e) {
			print "<h1>Mms MO API</h1>";
			print "<h3>Received Messages</h3>";
			print "<p>Exception faking MO ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}


		try{
			print "<table border=\"1\">";

			// Recieve one MMS with all the attachments

			$received_message = $mmsMO->getMessage(SHORTCODE,$message_identifier);

			// Show the returned information, only if DEBUG is set to true

			if (defined('DEBUG') && constant('DEBUG')) {
				print "<h3>Received Message</h3>";
				if($received_message) {
					print "<tr><td>Message</td><td>".$received_messages."</td></tr>";
				}
			}

		} catch(Exception $e) {
			print "<h3>Received Message</h3>";
			print "<p>Exception faking MO ".$e->getMessage()."</p>";
		}

		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}

		try{
			print "<table border=\"1\">";
			foreach ($attachments as $key => $value){

				// Get the attachment ID

				$attachmentID=$value->href;

				// Recieve MMS attachment

				$attachment = $mmsMO->getAttachment(SHORTCODE,$message_identifier,$attachmentID);

				// Show the returned information, only if DEBUG is set to true
					
				if (defined('DEBUG') && constant('DEBUG')) {
					print "<h3>Get Attachments</h3>";

					if($attachment){
						print "<tr><td>Attachment</td><td>".$attachmentID."</td></tr>";
					}
				}
			}
		} catch(Exception $e) {
			print "<h3>Get Attachments</h3>";
			print "<p>Exception faking MO ".$e->getMessage()."</p>";
		}
		
		// Show the http request and reply , only if DEBUG is set to true

		if (DEBUG){
			print "<tr><td>Request</td><td>".$bv->getLastRequest()."</td></tr>";
			print "<tr><td>Response</td><td>".$bv->getLastResponse()."</td></tr>";
			print "</table>";
		}
	}
	unset($mmsMO);
}
?>