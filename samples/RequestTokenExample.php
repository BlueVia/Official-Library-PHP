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
      'consumer_key' => 'XXXXXXXXXXX', 
      'consumer_secret' => 'YYYYYYYYYY'       
)
);
$bv=new BlueviaClient($mode,$application_context);

if ($bv)
{
	$oauth = $bv->getService('Oauth');
	if($oauth){
		try{
			
			// Request token. The callback parameter is set to null, this means you are going to
			// use the OutOfBound authorization. You can learn the three authorization ways
			// at https://bluevia.com/en/knowledge/libraries.PHP.oauth
			
			$request_token = $oauth->getRequestToken('oob');
			
			// Show the returned information, only if DEBUG is set to true
			
			if(DEBUG && $request_token) {
				print "<h1>Oauth API</h1>";
				print "<h3>Request token</h3>";
				print "<table border=\"1\">";
				print "<tr><td>Authorise URL</td><td><a href=".$request_token['oauth_url'].">".$request_token['oauth_url']."</a></td></tr>";
				print "<tr><td>Request Token</td><td>".$request_token['token']->getToken()."</td></tr>";
				print "<tr><td>Request Token Secret</td><td>".$request_token['token']->getTokenSecret()."</td></tr>";
				print "</table>";
			}

		} catch(Exception $e) {
			print "<h1>Oauth API</h1>";
			echo "<br><br><table><th>Request Token</th>";
			echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
		}
	}
}