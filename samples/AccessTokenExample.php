<?php

### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',true);

### Set the oauth verifier obtained at Bluevia Connect when the user authorizes the application
define('OAUTH_VERIFIER','XXXXX');

### Set the request token obtained when you call the request token function.
### You can learn how to get the request tokens in RequestTokenExample.php
define('REQUEST_TOKEN','XXXXXXXXXXXXXXXXXXXXX');
define('REQUEST_TOKEN_SECRET','YYYYYYYYYYYYYYYYYYYYY');

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
			// Sets Zend_Oauth_Token_Request object
			
			$request = new Zend_Oauth_Token_Request();
			$request->setParams(array (
					'oauth_callback_confirmed'=>'true',
					'oauth_token_secret'=>REQUEST_TOKEN_SECRET,
					'oauth_token'=>REQUEST_TOKEN
			));
			
			// Get Access Token
			
			$access_token = $oauth->getAccessToken(OAUTH_VERIFIER, $request);
			
			// Show the returned information, only if DEBUG is set to true
			
			if(DEBUG && $access_token){
				print "<h1>Oauth API</h1>";
				print "<h3>Access token</h3>";
				print "<table border=\"1\">";
				echo "<tr><td>Access Token</td><td>".$access_token['ACCESS_TOKEN']->getToken()."</td>";
				echo "<tr><td>Access Token Secret</td><td>".$access_token['ACCESS_TOKEN']->getTokenSecret()."</td></table>";
			}
		} catch(Exception $e) {
			print "<h1>Oauth API</h1>";
			echo "<br><br><table><th>Access Token</th>";
			echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
		}
	}
}
