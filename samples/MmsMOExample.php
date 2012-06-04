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

// The Mms MT constructor requires the application consumer key, consumer secret, token
// and token secret. The Mms MO only needs the consumer key and secret.

$consumerKey = "vw12012654505986"; 
$consumerSecret = "WpOl66570544"; 

$tokenAccess = "ad3f0f598ffbc660fbad9035122eae74"; 
$tokenSecret = "4340b28da39ec36acb4a205d3955a853"; 


/**** SEND THE MMS TO A SHORTCODE ****/
try{

	// Set the shortcode where the sms will be sent
	// Sending mms to a shortcode is only available on sandbox mode.
	$shortcode='5698765';

	$mmsMT= new BV_MTMms($mode,$consumerKey,$consumerSecret,$tokenAccess,$tokenSecret);

	print "<table border=\"1\">";

	// Send SMS

	// To check SMS sent to your application following a polling strategy the first word
	// in your sms text must be your application keyword (SANDBLUEDEMOS).
	$subject= 'SANDBLUEDEMOS Mms subject goes here.';

	$text = 'Your text message goes here.';

	$attachments=array();
	
	// Set the attachments. Uncomment the following lines and set the path toyour files.
//	$attachment1 = new Attachment('/path/to/image1.jpeg',BV_Mimetype::IMAGE_JPEG);
//	$attachment2 = new Attachment('/path/to/image2.gif',BV_Mimetype::IMAGE_GIF);
//	$attachments= array($attachment1,$attachment2);

	$messageId = $mmsMT->send($shortcode,$subject,$text,$attachments);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Mms MO API</h1>";
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

/**** RECIEVE ALL THE MMS SENT TO THIS SHORTCODE ****/
try {
	print "<table border=\"1\">";

	// Retrieve SMS delivery status
	$mmsMO= new BV_MoMms($mode,$consumerKey,$consumerSecret);

	$mmsInfo = $mmsMO->getAllMessages($shortcode,true);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Mms MO</h1>";
		print "<h3>All messages</h3>";
		if($mmsInfo) {
			foreach($mmsInfo as $id =>$info){
				print "<tr><td>Message $id</td></tr>";
				foreach($info as $key=>$value){
					print "<tr><td>$key</td><td>".$value."</td></tr>";
				}
			}
			print "</table>";
		}
	}
} catch (Exception $e) {
	print "<h3>Get All Messages</h3>";
	print "<p>Exception retreiving the MMS: ".$e->getMessage()."</p>";
}

/****** GET A COMPLETE MMS ***/
try {
	print "<table border=\"1\">";

	$messageId=$mmsInfo[0]->messageId;
	
	// Retrieve SMS delivery status
	$mmsMO= new BV_MoMms($mode,$consumerKey,$consumerSecret);

	$mms = $mmsMO->getMessage($shortcode,$messageId);

	// Show the returned information, only if DEBUG is set to true

	if (defined('DEBUG') && constant('DEBUG')) {
		print "<h1>Mms MO</h1>";
		print "<h3>Message info</h3>";
		if($mms) {

			print "<tr><td>Message $id</td></tr>";
			$message=$mms->mmsInfo;
			foreach($message as $key=>$value){
				print "<tr><td>$key</td><td>".$value."</td></tr>";
			}
			print "</table>";
			print "<table border=\"1\">";

			// Remove this block comment to write the attachments to a file.
			// You have to set the path where you have writing permission to the file before executing.

			/*$attachments = $mms -> attachments;
			 foreach($attachments as $id =>$attachment){
				print "<tr><td>Attachment $id</td></tr>";
				foreach ($attachment as $key=>$value){
				if ($key!='content'){
				print "<tr><td>$key</td><td>".$value."</td></tr>";
				}
				else {
				$file = fopen("/path/to/a/file","w");
				$a=fwrite($file,$value);
				fclose($file);
				}
				}
				}*/


		}

		print "</table>";

	}
} catch (Exception $e) {
	print "<h3>Get All Messages</h3>";
	print "<p>Exception retreiving the MMS: ".$e->getMessage()."</p>";
}


?>



