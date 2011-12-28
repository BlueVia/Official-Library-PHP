## System requirements ##

- [PHP Curl extension](http://php.net/manual/en/book.curl.php)
- [Zend Framework >= 1.10](http://framework.zend.com/)


## Programming guidelines ##

This section is a basic introduction to the Bluevia framework. This guide explains the library behavior and architecture, its working modes and the security model, based in OAuth, and how to start developing and testing PHP applications using the Bluevia APIs. In the API guides section several complete code examples for each API will be provided.

In order to complete the documentation of the Bluevia library, you should check the Reference section for API specifications.

## Bluevia library framework ##
PHP for BlueVia allows you to use the BueVia public API from your PHP application using just few lines of code. The library makes use of Zend Framework classes, more specifically 

- [Zend_Http](http://framework.zend.com/manual/en/zend.oauth.html)
- [Zend_Oauth](http://framework.zend.com/manual/en/zend.http.html)

When you want to use Bluevia public APIs as a developer, first of all you will need to get a commercial or testing API Key from [https://bluevia.com](https://bluevia.com) 

PHP SDK wraps requests to BlueVia APIs by using the generic object `BlueviaClient`. This object uses the Component Pattern to fetch any service required by the developer (oAuth, SMS, MMS, Directory or Advertising).

This snippet shows the easiest way to create a new object with valid credentials that identify the application:

    $application_context = array(
       'app' => array(
         'consumer_key' => CONSUMER_KEY,
         'consumer_secret' => CONSUMER_SECRET
       )
    );
    $bv = new BlueviaClient($application_context);
 
## Endpoints ##

BlueVia has two endpoints: live and sandbox. Live is linked to Telefonica's network while sandbox is used just to verify application behavior. You can activate the sandbox mode by setting the following option:

    BlueviaClient_Api_Constants::$environment  = BlueviaClient_Api_Constants::ENVIRONMENT_SANDBOX;
    
You can print the variable to verify which environment is being used:

    print BlueviaClient_Api_Constants::$environment;
    
## Using OAuth ##

User authentication is performed using oAuth protocol, so user is not required to use credentials in third party applications. If you want to learn more about oAuth please check this URL: [http://oauth.net](http://oauth.net).

When an user wants to launch the oAuth process, once the Bluevia client object has been created only the two lines below are required to retrieve a valid token for user:

    $oauth = $bv->getService('Oauth');
    $token = $oauth->getRequestToken(CONSUMER_KEY, CONSUMER_SECRET, APP_URL);
    
The retrieved parameter token and secret should be used during the oAuth process, and url is the endpoint where Bluevia shall authenticate the user. In case of a PHP application, it is automatically performed using Zend_Oauth library.

Both token and token_secret must be saved in the client side because oAuth process will require it later. They are stored inside library wrapper: Oauth.php as a cookie.

    setcookie('req_token', serialize($token), null, '/');
    
Once user is authenticated and he has authorized the application in BlueVia portal, he should be redirected to the URL used as parameter before (APP_URL). Now it's time to fetch the valid token and token secret that shall identify the new user during any call to BlueVia API. Lines below show an example using PHP:

    $oauth_verifier = $_GET['oauth_verifier'];
    $returned = $oauth->getAccessToken($oauth_verifier, CONSUMER_KEY, CONSUMER_SECRET);
    
If token verifier is bad, or cookie has been lost, then `BlueviaClient_Exception_Parameters` is thrown.

## Using OAuth to launch requests ##

Most of requests when accessing Bluevia API are associated to a specific user, so when a `BlueviaClient` object is created both user token and user token secret must be provided to identify user on behalf of whom the application wants to access BlueVia APIs. It is responsibility of the application to store it and they are send instantiating Unica API.

    $application_context = array(
        'user' => array(
          'token_access' => TOKEN,
          'token_secret' => TOKEN_SECRET,
        ),
        'app' => array(
          'consumer_key' => CONSUMER_KEY,
          'consumer_secret' => CONSUMER_SECRET        
        )
    );

    $bv = new BlueviaClient($application_context);
  
## API guides ##

The following guides explain the behavior of each Bluevia API, including code samples to start developing a simple application in an easy way:

- SMS API
- MMS API
- User Context API
- Advertising API
- Location API
- Payment API

They can be found as part of the library documentation.