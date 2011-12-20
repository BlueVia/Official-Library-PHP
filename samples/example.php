<?php

### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',false);
### Set the recepient of the SMS
define('SMS_RECIPIENT',000000000);
### Set the country shortcode to get the messages 
define('SHORTCODE',000000);

### Set your Zend Framework, pear and php local path
$oldpath = set_include_path('.:/usr/local/zend/share/ZendFramework/library:/usr/local/zend/share/pear:/usr/share/php');
### Set your BlueviaClient.php path
include_once "../src/BlueviaClient.php";

// BlueVia provides two environments to support the different development stages of your app. 
// Sandbox for testing, and Live for accessing the live network.
// You can choose which of them to use depending on the API endpoint you need.
BlueviaClient_Api_Constants::$environment  = BlueviaClient_Api_Constants::ENVIRONMENT_SANDBOX;

// PHP SDK wraps any request to BlueVia API's by using a generic object BlueviaClient. 
// This object uses the Component Pattern to fetch any service required by the developer (oAuth, SMS, Directory or Advertising).
// The BlueviaClient constructor requires an array containing the application consumer key, consumer secret and the access token data 
$application_context = array(
    'app' => array(
      'consumer_key' => 'XXXXXXXXXXXXXXXX', //CONSUMER_KEY,
      'consumer_secret' => 'YYYYYYYYYYYY' //CONSUMER_SECRET        
    ),
	'user' => array(
      'token_access' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', //TOKEN,
      'token_secret' => 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY' //TOKEN_SECRET,
    )
  );
 
$bv = new BlueviaClient($application_context);
if ($bv)
{	
	$sms = $bv->getService('Sms');	
	if ($sms)
	{
		// Sets an address element containing the phone number to which this message is sent		
		$sms->addRecipient(SMS_RECIPIENT);
		// Sets a message element containing the message itself
		$sms->setMessage('Your message goes here');
		
		// Send SMS
		try{							
			$result = $sms->send();			
			if (defined('DEBUG') && constant('DEBUG')) {
				if($result) var_dump($result);
				print "<p style=\"color:blue\"> Request: ".$bv->getLastRequest()."</p>";
				print "<p style=\"color:blue\"> Response: ".$bv->getLastResponse()."</p>";
			}				
		} catch (Exception $e) {
			print "<p> Error sending SMS: ".$e->getMessage()."</p>";
		}
		
		// Retrieve SMS delivery status
		try {
			$delivery_status = $sms->getDeliveryStatus($resultado);			
			if (defined('DEBUG') && constant('DEBUG')) {
				if($delivery_status) var_dump($delivery_status);
				print "<p style=\"color:blue\"> Request: ".$bv->getLastRequest()."</p>";
				print "<p style=\"color:blue\"> Response: ".$bv->getLastResponse()."</p>";
			}	
		} catch (Exception $e) {
			print "<p>Error retreiving the SMS delivery status: ".$e->getMessage()."</p>";
		}
						
		// Get messages
		try {			
			$received_messages = $sms->getMessages(SHORTCODE);
			if (defined('DEBUG') && constant('DEBUG')) {
				if($received_messages) var_dump($received_messages);
				print "<p style=\"color:blue\"> Request: ".$bv->getLastRequest()."</p>";
				print "<p style=\"color:blue\"> Response: ".$bv->getLastResponse()."</p>";
			}
		} catch(Exception $e) {
			print "<p>Error retrieving SMS".$e->getMessage()."</p>";
		}
							
		unset($sms);				
	} 			
}
?>