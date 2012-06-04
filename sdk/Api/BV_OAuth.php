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
 *  This class provides access to the set of functions to complete the OAuth workflow to
 *  retrieve the OAuth credentials for Bluevia applications
 *
 * @author Telefonica R&D
 *
 */
class BV_OAuth extends BV_OAuth_Client{


	/**
	 * Oauth constructor
	 * @param BV_Mode $mode BlueVia provides three modes to support the different
	 * 						development stages of your app. [LIVE,TEST,SANDBOX]
	 * @param String $consumer The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @throws Bluevia_Exception
	 */
	public function __construct($mode,$consumer,$consumerSecret){
		$this->checkTwoLeggedCredentials($consumer,$consumerSecret);
		parent::initUntrusted($mode,$consumer,$consumerSecret);
		$this->setParameters();
	}

	/**
	 * Retrieves a request token
	 * @param String|null $callback (Optional) the callback to redirect the application once the request token has been authorized
	 * @param  boolean $autoRedirect If true creates token cookie
	 *                               and redirects automatically to Bluevia's
	 *                               portal. (BV_Constants::AUTH_URL_TEST or BV_Constants::AUTH_URL_LIVE). Default to true
	 * @return Request_Token the request token
	 * @throws Bluevia_Exception
	 */
	public function getRequestToken($callback = null,
	$autoRedirect = true){
		return parent::getRequestToken($callback,$autoRedirect);
	}

	/**
	 * Retrieves a request token using the Bluevia SMS handshake
	 * The oauth verifier will be received vía SMS in the phone number specified as a parameter, instead of
	 * getting a verification url.
	 *
	 * @param String $phoneNumber the phone number to receive the SMS with the oauth verifier (PIN code)
	 * @throws Bluevia_Exception
	 */
	public function getRequestTokenSmsHandshake($phoneNumber) {
		return parent::getRequestTokenSmsHandshake($phoneNumber);
	}

	/**
	 * Retrieves the access token corresponding to request token parameter
	 *
	 * @param String $oauthVerifier PIN code obtained in Bluevia's portal
	 * @param String|null $requestToken (Optional) The token -a string- used by the client for granting access permissions to the server.
	 * @param String|null $requestSecret (Optional) The secret of the request token.
	 * @return Token the access token
	 * @throws Bluevia_Exception
	 */
	public function getAccessToken(
	$oauthVerifier,
	$requestToken = null, $requestSecret = null) {
		return parent::getAccessToken($oauthVerifier,$requestToken,$requestSecret);
	}

}