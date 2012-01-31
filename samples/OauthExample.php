<?php

### Set DEBUG true to see the http request and reply headers and content
define('DEBUG',true);

### Set the callback URL, where you want to recieve the Oauth verifier.
define('CALLBACK_URL',$_GET['original_url']);

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


		if (empty($_GET['oauth_verifier'])){
			try {

				// Request token. Callback parameter is a defined callback URL. This means you are
				// going to use the WebOauth authorization. You can learn the three authorization
				// ways at https://bluevia.com/en/knowledge/libraries.PHP.oauth

				$request_token = $oauth->getRequestToken(CALLBACK_URL);
			} catch(Exception $e) {
				print "<h1>Oauth API</h1>";
				echo "<br><br><table><th>Request Token</th>";
				echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
			}
		}else{
			try {
				
				// Obtain the request token returned by the getRequestToken function.
				
				$req_token=unserialize($_COOKIE['req_token']);
				
				// Get the Access Token
				
				$access_token = $oauth->getAccessToken($_GET['oauth_verifier'], $req_token);
				
				// Show the returned information, only if DEBUG is set to true
				
				if (DEBUG){
					print "<h1>Oauth API</h1>";
					echo "<br><br><table border=\"1\">";
					echo "<tr><td>Consumer Key</td><td>".$application_context['app']['consumer_key']."</td>";
					echo "<tr><td>Consumer Secret</td><td>".$application_context['app']['consumer_secret']."</td>";
					echo "<tr><td>Request Token</td><td>".$req_token->getToken()."</td>";
					echo "<tr><td>Request Token Secret</td><td>".$req_token->getTokenSecret()."</td>";
					echo "<tr><td>Oauth verifier</td><td>".$_GET['oauth_verifier']."</td>";
					echo "<tr><td>Access Token</td><td>".$access_token['ACCESS_TOKEN']->getToken()."</td>";
					echo "<tr><td>Access Token Secret</td><td>".$access_token['ACCESS_TOKEN']->getTokenSecret()."</td></table>";
				}
				
			} catch(Exception $e) {
				echo "<br><br><table><th>Access Token</th>";
				echo "<table><tr><td>Exception: ".$e->getMessage()."</td></tr></table>";
			}

		}

	}
	unset($oauth);
}