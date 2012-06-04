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

// PHP SDK wraps any request to BlueVia API's by using a generic object BlueviaClient.
// This object uses the Component Pattern to fetch any service required by the developer (oAuth, SMS, Directory or Advertising).
// The BlueviaClient constructor requires an array containing the application consumer key, consumer secret and the access token data

$consumerKey = "vw12012654505986";
$consumerSecret = "WpOl66570544";


$token_access = "ad3f0f598ffbc660fbad9035122eae74"; //TOKEN,
$token_secret = "4340b28da39ec36acb4a205d3955a853"; //TOKEN_SECRET,

try{

	### Set the recepient of the SMS
	$destination = '33333333333';

	$mmsMT= new BV_MTMms($mode,$consumerKey,$consumerSecret,$token_access,$token_secret);

	print "<table border=\"1\">";

	// Send SMS

	$attachments=array();
	// To send a mms with attachments remove the above comment and set the paths to your attachments
	
	//	$attachment1=new Attachment('/path/to/your/attachment1.jpeg',BV_Mimetype::IMAGE_JPEG);
	//	$attachment2=new Attachment('/path/to/your/attachment2.txt',BV_Mimetype::TEXT_PLAIN);
	//	$attachments=array($attachment1,$attachment2);
	
	$messageId = $mmsMT->send($destination,'The messages subject goes here.', 'MMs text message',$attachments);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Mms MT API</h1>";
		print "<h3>Send MMS</h3>";
		if($messageId) {
			print "<tr><td>Mms ID</td><td>".$messageId."</td></tr>";
		}
		print "</table>";
	}
} catch (Exception $e) {
	print "<h1>Mms MT</h1>";
	print "<h3>Send MMS</h3>";
	print "<p> Exception sending MMS: ".$e->getMessage()."</p>";
}


try {
	print "<table border=\"1\">";

	// Retrieve SMS delivery status

	$delivery_status = $mmsMT->getDeliveryStatus($messageId);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h3>Delivery status</h3>";
		if($delivery_status) {
			foreach($delivery_status as $id =>$status){
				print "<tr><td>Status $id</td></tr>";
				foreach($status as $key=>$value){
					print "<tr><td>$key</td><td>".$value."</td></tr>";
				}
			}
			print "</table>";
		}
	}
} catch (Exception $e) {
	print "<h3>Delivery status</h3>";
	print "<p>Exception retreiving the MMS delivery status: ".$e->getMessage()."</p>";
}

unset($mmsMT);


?>


