<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2010 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */

/**
 * Abstract client for the REST binding of the Bluevia OAuth Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_OAuth_Client extends BV_Base_Client
{

	/**
	 *
	 * XML_Parser $_errorParser. Parser used when error.
	 */
	private $_errorParser;

	/**
	 * Obtain request token
	 * @param  string  $callbackUrl Current Application URL or MSISDN for the SMS handshake
	 * @param  boolean $autoRedirect If true creates token cookie
	 *                               and redirects automatically to Bluevia's
	 *                               portal. (BV_Constants::AUTH_URL_TEST or BV_Constants::AUTH_URL_LIVE). Default to true
	 * @return Request_Token|null If $autoRedirect is set to true, the application will redirect the user to Bluevia's portal so this function will return nothing. The tokens will be saved in a cookie.
	 * @throws Bluevia_Client
	 */
	protected function getRequestToken(
	$callback = null,
	$autoRedirect = true) {
		$this->checkCallbackUrl($callback);
		try{
			$response = $this->baseCreate(BV_Constants::REQ_URL);
			return $this->createRequestTokenResponse($response,$callback,$autoRedirect);
		}catch (Connector_Exception $e){
			$this->_parseError($e,$this->_errorParser);
		}
			
	}

	/**
	 * Helper function to simplify the server's response to the getRequestToken method into a more useful object.
	 * Also redirects to the Bluevia portal so the user can authorize the application if autoRedirect is set to true and the callback url is set
	 * @param array $response Parsed response from the server.
	 * @param String|null $callback the URL where you want the user to be redirect after the authorization step.
	 * @param Bool $autoRedirect Default to true. If set to true the user will be redirect automatically to the Bluevia's portal.
	 * @return Request_Token if the user has not been redirect to Bluevia's portal. In other case the request tokens will be saved in a cookie.
	 */
	protected function createRequestTokenResponse($response,$callback=null,$autoRedirect=true){
		$token = new Request_Token();
		$token=$this->simplifyResponse($token,$response);
		if ($this->_mode===BV_Mode::LIVE){
			$authURL=BV_Constants::AUTH_URL_LIVE;
		}else{
			$authURL=BV_Constants::AUTH_URL_TEST;
		}
		$token->authUrl=$authURL.'?oauth_token='.$token->key;
		// store token: we will need later for getToken Method
		// Cookie support is required!
		$set = setcookie('req_token', serialize($token), null, '/');
		// redirect to oAuth Portal, allowing the user to authorize app
		if ($autoRedirect && Utils::isValidURL($callback)) {
			$this->autoRedirect($token);
		} else {
			return $token;
		}

	}

	/**
	 * Retrieves a request token using the Bluevia SMS handshake
	 * The oauth verifier will be received vía SMS in the phone number specified as a parameter, instead of
	 * getting a verification url.
	 *
	 * @param String $phoneNumber the phone number to receive the SMS with the oauth verifier (PIN code)
	 * @return Token the request token
	 * @throws Bluevia_Exception
	 */
	protected function getRequestTokenSmsHandshake($phoneNumber) {

		$this->_checkPhoneNumberCallback($phoneNumber);

		try{
			$response = $this->baseCreate(BV_Constants::REQ_URL);
			$token = new Token();
			$token=$this->simplifyResponse($token,$response);
			return $token;
		}catch (Connector_Exception $e){
			$parser= new XML_Parser();
			$this->_parseError($e,$this->_errorParser);
		}
	}


	/**
	 * Retrieves the access token corresponding to request token parameter
	 *
	 * @param String $oauthVerifier PIN code obtained in Bluevia's portal
	 * @param String|null $requestToken (Optional) The token -a string- used by the client for granting access permissions to the server.
	 * @param String|null $requestSecret (Optional) The secret of the request token.
	 * @return Token the access tokens
	 * @throws Bluevia_Exception
	 */
	protected function getAccessToken(
	$oauthVerifier,
	$requestToken = null, $requestSecret = null) {
		$this->checkAccessParams($oauthVerifier,$requestToken,$requestSecret);
		try{
			$response=$this->baseCreate(BV_Constants::ACC_URL);

			return $this->simplifyResponse(new Token(),$response);
			// is responsability of the application to mantain Token + Token Secret
		}catch (Connector_Exception $e){
			$this->_parseError($e,$this->_errorParser);
		}

	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters(){
		$this->_parser= new URLEncoded_Parser();
		$this->_errorParser = new XML_Parser();
		$this->_url = BV_Constants::BASE_URL.BV_Constants::REST_URL.BV_Constants::OAUTH_URL;
	}

	/**
	 * Helper function to redirect to the Bluevia's portal url.
	 * @param String $response Two posible values: BV_Constants::AUTH_URL_TEST or BV_Constants::AUTH_URL_TEST
	 */
	protected function autoRedirect($response){
		header("Location: ".$response->authUrl);
		exit();
	}

	/**
	 * Simplify the server's response to the BV_OAuth::getRequestToken method and the BV_OAuth::getRequestTokenSmsHandshake into a more useful object
	 * @param Token|Request_Token $token The object to be set with the information in $response.
	 * @param stdClass $response a standard object with all the infomation returned from the server.
	 * @return Token|Request_Token Object containing the useful information from the server. Depends on which $token has been passed
	 */
	protected function simplifyResponse($token,$response){
		$token->key=$response['oauth_token'];
		$token->secret=$response['oauth_token_secret'];
		$this->_connector->setTokens($token->key,
											$token->secret);
		return $token;
	}

	/**
	 * Check if the $callbackUrl has a valid format. If is valid the connector will be set with this url.
	 * @param String $callbackUrl URL where the verifier code for the OAuth process, is going to be sent.
	 * @return String with the valid callback url
	 */
	protected function checkCallbackUrl ($callbackUrl){
		if (empty($callbackUrl) || is_null($callbackUrl) ) {
			$callbackUrl='oob';
		}

		if (!(Utils::isValidURL($callbackUrl)
		||  $callbackUrl ==='oob') || is_numeric($callbackUrl)){
			throw new Bluevia_Exception('-106',null,'$callbackUrl',' a URL, or empty.');
		}

		$this->_connector->setExtraParameters(array('oauth_callback'=>$callbackUrl));
		return $callbackUrl;
	}


	/**
	 * Helper function to check the BV_OAuth::getAccessToken method parameter's
	 * @param String $oauthVerifier PIN code obtained in Bluevia's portal
	 * @param String|null $requestToken (Optional) The token -a string- used by the client for granting access permissions to the server.
	 * @param String|null $requestSecret (Optional) The secret of the request token.
	 * @return Token the access token
	 */
	protected function checkAccessParams($oauthVerifier,$requestToken=null,$requestSecret=null){

		// get request token from parameters or from cookie: @see getRequestToken
		if (empty($requestToken) && !empty($_COOKIE['req_token'])) {
			$requestToken = unserialize($_COOKIE['req_token']);
			$requestSecret=$requestToken->secret;
			$requestToken=$requestToken->key;
		}
		if (empty($requestToken) || empty($requestSecret)){
			if ($this->_connector->isTwoLegged()){
				throw new Bluevia_Exception('-108', null, 'request tokens');
			}
		}
		else{
			$this->_connector->setTokens($requestToken,$requestSecret);
		}
		$this->_connector->setExtraParameters(array('oauth_verifier'=>$oauthVerifier));

		// for console authorisation
		if (empty($_GET['oauth_verifier'])) {
			$_GET['oauth_verifier'] = $oauthVerifier;
		}
	}

	/**
	 * Checks if the parameter $phoneNumber is a valid phone number
	 * @param String $phoneNumber 
	 */
	private function _checkPhoneNumberCallback($phoneNumber){
		Utils::isPhoneNumber($phoneNumber);
		if ($this->_mode!=BV_Mode::LIVE){
			throw new Bluevia_Exception('-7',null,'OAuth by SmsHandshake','LIVE mode.');
		}
		$this->_connector->setExtraParameters(array('oauth_callback'=>$phoneNumber));
	}
}
