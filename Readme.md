## Set up your environment

This section explains how to prepare your development environment to start working with the Bluevia PHP SDK. First check out the system requirements that your computer must meet, and then follow the installation steps. Once you have finished you will be able to develop your first Android application using the functionality provided by Bluevia APIs.

### System requeriments

The Bluevia library for PHP is prepared and has been tested to develop applications under PHP SDK 5.3 versions. 

PHP SDK is the only system requirement for the Bluevia library. The following system requirements are the ones your computer needs to meet to be able to work with the PHP SDK:

Tested Operating Systems:

	- Mac OS X 10.6.8
	- Linux (tested on Linux Ubuntu)

Developing environment:

	- PHP 5.3
	- Eclipse PDT for PHP developers.
  
The following PEAR packages are required:
 
	- HTTP_Request2. (http://pear.php.net/package/HTTP_Request2/).
	- Mail_mimeDecode.(http://pear.php.net/package/Mail_mimeDecode/)

### Step 1: Preparing the PHP environment
The first step to start developing applications is setting up your PHP environment. If you have already prepared your computer to develop PHP applications you can skip to step 2; otherwise follow the next instructions:

	- Prepare your development computer and ensure it meets the system requirements.
	- Install PHP.
	- Install Eclipse PDT (if you'll be developing in Eclipse). (http://www.eclipse.org/pdt/downloads/)
	- Once you have installed PHP you'll have to install the pear packages listed above. You can find complete information on how to install pear packages at http://pear.php.net/manual/en/installation.php

Visit php.net for complete instructions: [downloading and installing PHP](http://php.net/manual/en/install.php).

### Step 2: Download Bluevia library for PHP and create the project

	- Create your PHP Project in Eclipse: select File > New > PHP Project. 
	- Introduce the project name and click Finish.</li>
  
  \subsection getting_started_sec_step3 Step 3: Setting the include path:
  
  Include the Bluevia Library and the PEAR packages into the include path. You must include the following lines in your project:
  
  @code
  set_include_path(".:path/to/pear/package:path/to/bluevia/sdk");
  include_once 'sdk/Includes.php';
  @endcode
  
  Remember the include path must be the path were you downloaded PEAR packages and the one with Bluevia's SDK separated with a colon character <strong>( : )</strong>.
  
 If you are using Eclipse, in order to provide proper auto completion and error checking, you can add the Bluevia external libraries to the include paths option:
<ol>
<li>Select Project > Properties > PHP Include Path</li>
<li>Select libraries > Add External Folder</li>
<li> Search for the path to Bluevia's SDK</li>
<li> Click OK.</li>
</ol>


## Code samples 
You can find a set of complete sample apps on this repository:

- [/samples/PaymentExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/PaymentExample.php) : Performs a Payment 
- [/samples/OAuthExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/OAuthExample.php) : Demostrates OAuth process negotiation
- [/samples/SmsMTExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/SmsMTExample.php) : Sends SMS and a Check Delivery Status
- [/samples/SmsMOExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/SmsMOExample.php : Receive SMS.
- [/samples/MmsMTExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/MmsMTExample.php) : Sends MMS and a Check Delivery Status
- [/samples/MmsMOExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/MmsMOExample.php) : Receive MMS.
- [/samples/LocationExample.php)](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/LocationExample.php) : Gets the location of a user
- [/samples/DirectoryExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/DirectoryExample.php) : Gets user and user equipement information
- [/samples/AdvertisingExample.php](https://github.com/BlueVia/Official-Library-PHP/tree/master/samples/AdvertisingExample.php) : Gets advertising

Please find below also some quick snippets on how to use the library.


### OAuth proccess negotiation
Most of the APIs need have passed a complete OAuth process once before starting to use them because they will act on behalf a customer (OAuth 3-leggded mode); others, like receiving messages ones, don't need that process (OAuth 2-legged mode). The advertising API, could be used both as 3-legged and as 2-legged.

#### Step 1: Get application keys (consumer keys).
You can get your own application keys for you app at [BlueVia] (https://bluevia.com/en/page/tech.howto.tut_APIkeys).

#### Step 2: Init oauth process: Do a request tokens
BlueVia APIs authentication is based on [OAuth 1.0](https://bluevia.com/en/page/tech.howto.tut_APIauth)
To get the users authorization for using BlueVia API's on their behalf, you shall do as follows.
By using your API key, you have to create a request token that is required to start the OAuth process. For example:

  // Create the client (you have to choose the mode (LIVE|SANDBOX|TEST) and include the Consumer credentials)
  $oauthClient = new BV_OAuth(BV_Mode::LIVE, "my_consumer_key", "my_consumer_secret");
  // Retrieve the request token
  $requestToken = oauthClient->getRequestToken();

#### Step 3: User authorisation

There are three alternatives to request the user authorisation:

  - Callback authorisation

  Callback parameter is  a defined callback URL. You will receive the oauth_verifier as a request parameter at your callback.
  // Retrieve the request token
  $oauthClient->getRequestToken("http://foo.bar/bluevia/get_access")

The user will be redirect to the Bluevia's verification Url, so he can authorize the application. Once he has finished, he will be redirect again to the application so he can complete the OAuth's proccess. Your application will recieve the oauth_verifier in the url as a query string. To get the oauth_verifier, once your application is executing again you can use this code:

  // Get the oauth_verifier
  $oauthVerifier=$_GET['oauth_verifier'];

Request Tokens are stored in a cookie, so if you want to use this authorization method, cookies must be enabled. If you don't want your application to be redirect automatically to the Bluevia Portal, you can set the autoredirect param to false.

  - OutOfBand authorisation
To get user authorization using the oauth_token from your request token you have to take the user to BlueVia. The obtained request token contains the verification url to access to the BlueVia portal. Depending on the mode used, it will be available for final users (LIVE) or developers (TEST and SANDBOX). The application should enable the user (customer) to visit the url in any way, where he will have to introduce its credentials (user and password) to authorise the application to connect BlueVia APIs behalf him. Once permission has been granted, the user will obtain a PIN code necessary to exchange the request token for the access token:
  // Open the received url in a browser
  $url=$requestToken->authUrl;
  header("Location: ".$url);
  exit();

Once the user confirms the authorization, you have to ask the user to enter the oauth_verifier in your app. Note that your users will need to copy and paste the oauth_verifier manually, so be clear when you request it to be sure they do not get confused.

  - SMSOauth authorisation
Bluevia supports a variation of OAuth process where the user is not using the browser to authorize the application. Instead he will receive an SMS containing he PIN code (oauth_verifier). To use this SMS handshake, getRequestToken request must pass the user's MSISDN (phone number) in callback parameter. After the user had received the PIN code, the application should allow him to enter it and request the access token.
  // Retrieve the request token
  $requestToken = $oauthClient->getRequestToken("34609090909");

#### Step 4: Get access tokens
With the obtained PIN code (oauth_verifier), you can now get the accessToken from the user as follows:
  //Obtain the access token
  $accessToken = $oauthClient->getAccessToken(oauth_verifier,$requestToken->key,$requestToken->secret);

Both token and token_secret must be saved in your application because OAuth process will require it later.


### Payment API
Payment API enables your application to make payments behalf the user to let him buy products or pay for services, and request the status of a previous payment.
Bluevia Payment API uses an extension of OAuth protocol to guarantee secure payment operations. For each payment the user makes he must complete the OAuth process to identify itself and get a valid acess token. These tokens will be valid for 48 hours and then will be dismissed.
First, you have to retrieve a request token to be authorised by the user. In this case you have to use the PaymentRequestToken object, which includes the digital good pricing besides the usual request tokens params:
  $paymentClient = new BV_Payment(BV_Mode::LIVE, "consumer_key", "consumer_secret");
  $requestToken = $paymentClient->getRequestToken(100, "GBP", $serviceName, $serviceId);

Note that the callBackUrl is an optional value, you can set it to null if your application is not able to recieve request from BlueVia. Typically websites set a callBackURL and desktop or mobile applications don't.
Then, take the user to BlueVia Connect to authorise the application as usual.
Once you have obtained the oauth_verifier, you can now get the accessToken as follows:
  $accessToken = $paymentClient->getAccessToken($verifier, $requestToken->key,$requestToken->secret); /* Get verifier from GUI */

	
### Send SMS and get delivery status
SMS API allows your app to send messages on behalf of the users, this means that their mobile number will be the text sender and they will pay for them.

#### Sending SMS
  $smsClient = new BV_MtSms(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret","token", "token_secret");
  // Send the message.
  $smsId = $smsClient->send($destination, $text);

Your application can send the same SMS to several users including an array with multiple destinations.
  // Add multiple destinations.
  $destination= array('44123456789',4490090009000);
  
Take into account that the recipients numbers are required to included the international country calling code.

#### Checking delivery status
After sending an SMS you may need to know if it has been delivered. 
You can poll to check the delivery status.This alternative is used typically for mobile applications without a backend server.
You need to keep the deliveryStatusId to ask about the delivery status of that SMS as follows:
  $status = $smsClient->getDeliveryStatus($smsId);  
  foreach($delivery_status as $id =>$status){
     print "<tr><td>Status $id</td></tr>";
     foreach($status as $key=>$value){
	 print "<tr><td>$key</td><td>".$value."</td></tr>";
     }
  }	

### Send MMS and get delivery status 
MMS API enables your application to send an MMS on behalf of the user, check the delivery status of a sent MMS and Receive an MMS on your application.

#### Sending MMS
  $mmsClient = new BV_MtMms(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret", "accessToken", "accessTokenSecret");
  Several attachments could be attached to the MMS message. The class that represent multipart attachment is Attachment:

  // Create the Attachment object
  $attachments=array();
  $attachments[]= new Attachment("/path/to/image/image.gif",BV_Mimetype::IMAGE_GIF);
  $attachments[]= new Attachment("/path/to/image/image2.jpeg",BV_Mimetype::IMAGE_JPEG);

  // Send the message.
  $mmsId = $mmsClient->sendMms($destination,$subject,$text,$attachments);

Your application can send the same MMS to several users including an array with multiple destinations.
  // Add multiple destinations.
  $destination= array('44123456789',4490090009000);

#### Checking delivery status
After sending an MMS you may need to know if it has been delivered.
You can poll polling to check the delivery status. This alternative is used typically for mobile applications without a backend server.
You need to keep the deliveryStatusId to ask about the delivery status of that MMS as follows:
  $delivery_status = $mmsClient->getDeliveryStatus($mmsId);  
  foreach($delivery_status as $id =>$status){
     print "<tr><td>Status $id</td></tr>";
     foreach($status as $key=>$value){
	 print "<tr><td>$key</td><td>".$value."</td></tr>";
     }
  }

### Receive SMS 
You can can retrieve the SMS sent to your app using OAuth-2-legged auhtorisation so no user access token is required.
  $smsClient = new BV_MoSms(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret");

Your application can receive SMS from users sent to [BlueVia shortcodes](http://bluevia.com/en/page/tech.overview.shortcodes) including your application keyword. You have to take into account that you will need to remember the SMS keyword you defined when you requested you API key.

You can grab messages sent from users to you app as follows:
  $registrationId = "553456"
  $smsInfo = $smsMo->getAllMessages($registrationId);

Note that this is just an example and you should implement a more efficient polling strategy.

### Receive MMS 
You can can retrieve the MMS sent to your app using OAuth-2-legged auhtorisation so no user access token is required.
  $mmsClient = new BV_MoMMS(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret");

Your application can receive MMS from users sent to [BlueVia shortcodes](http://bluevia.com/en/page/tech.overview.shortcodes) including your application keyword. You have to take into account that you will need to remember the MMS keyword you defined when you requested you API key. 

You can grab messages sent from users to you app as follows. The ReceivedMmsInfo object contains the information of the sent MMS, but the attachments. In order to retreive attached documents in the MMS you have to use the getMessage function, which needs the messageIdentifier available in the ReceivedMmsInfo object. The returned ReceivedMms object contains the info of the Mms itself and a list of MimeContent objects with the content of the attachments:
  $registrationId = "553456"
  $list = $mmsClient->getAllMessages($registrationId);   
  foreach ($list as $key => $message){         
    $mms = $mmsClient.getMessage($registrationId, $message->messageId);
    print "Subject: " . $mms->subject;
    for ($mms->attachments as $attKey => $attachment){
      // You can save on do any stuff with the attachments
    }
  }
Note that this is just an example and you should implement a more efficient polling strategy


### User Context API
User Context API enables your application to get information about the user's customer profile in order to know more about your users to targetize better your product.

  // Create the Directory Client
  $directoryClient= new BV_Directory(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret","token", "token_secret");

#### Getting Profile Information
  $profile = $directoryClient->getProfile();

#### Getting Access Information
  $accessInfo = $directoryClient->getAccessInfo();

#### Getting Device Information
  $terminalInfo = $directoryClient->getTerminalInfo();

#### Filters
If you want to configure a filter on the information relevant for your application you can do it for any of the requests above:

  $fields = array(Profile_Fields::USER_TYPE, Profile_Fields::PARENTAL_CONTROL, Profile_Fields::OPERATOR_ID);
	
  //Get the Profile
  $profile = $directoryClient->getProfile($fields);

#### Getting all User Information

  $info = $directoryClient->getUserInfo();

### Location API
Location API enables your application to retrieve the geographical coordinates of user. These geographical coordinates are expressed through a latitude, longitude, altitude and accuracy.

The acceptableAccuracy (optional) parameter expresses the range in meters that the application considers useful. If the location cannot be determined within this range, then the application would prefer not to receive the information.

  $locationClient = new BV_Location(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret","token", "token_secret");
  $acceptableAccuracy = 500;
  $location = $locationClient->getLocation($acceptableAccuracy);

### Advertising API
Adverstising API enables your application to retrieve advertisements. 

You can invoke this API using a 3-leddged client (ouath process passed) or a 2-legged client. This is selected in the client instantiating.
Once configured your client is ready to get advertisements. When retrieving a simple advertisement you can specify a set of request parameters such as banner size, protection policy, etc. Mandatory parameters are adSpace, that is the identifier you obtained when you registered your application within the Bluevia portal; and protectionPolicy. The adRequetsId is an optional parameter (if it is not supplied, the SDK will generate one). For a more detailed description please see the API Reference.
  $adClient = new BV_Advertising(BV_Mode::LIVE, "my_consumer_key","my_consumer_secret","token", "token_secret");
  $response = $adClient->getAdvertising($adSpace, $adId, Ad_Presentation::IMAGE, null, Protection_Policy::SAFE);

Take into account that the Protection Policy sets the rules for adult advertising, please be careful.
  Protection_Policy::LOW 	Low, moderately explicit content (I am youth; you can show me moderately explicit content).
  Protection_Policy::SAFE 	Safe, not rated content (I am a kid, please, show me only safe content).
  Protection_Policy::HIGH 	High, explicit content (I am an adult; I am over 18 so you can show me any content including very explicit content).