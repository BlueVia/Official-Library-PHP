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
 * Client interface for the REST binding of the Bluevia Adverising Service.
 *
 * @author Telefonica R&D
 * 
 */
class BV_Advertising extends BV_Advertising_Client{


	/**
	 * Advertising API constructor
	 * @param BV_Mode $mode BlueVia provides three modes to support the different
	 * 						development stages of your app. [LIVE,TEST,SANDBOX]
	 * @param String $consumer The string identifying the application- you obtained when you registered your application within the provisioning portal.
	 * @param String $consumerSecret A secret -a string- used by the consumer to establish ownership of the consumer key.
	 * @param String|null $token (Optional) The token -a string- used by the client for granting access permissions to the server.
	 * @param String|null $tokenSecret (Optional) The secret of the access token.
	 * @throws Bluevia_Exception
	 */
	public function __construct($mode,$consumer,$consumerSecret,$token=null,$tokenSecret=null){
		$this->checkTwoLeggedCredentials($consumer,$consumerSecret);
		parent::initUntrusted($mode,$consumer,$consumerSecret,$token,$tokenSecret);
		$this->setParameters();
	}

	/**
	 * Requests the retrieving of an advertisement.
	 * This function can only be used in 3-legged-mode (OAuth token must have been included in the construction of the client)
	 *
	 * @param string $adSpace the adSpace of the Bluevia application
	 * @param string|null $adRequestId (optional) an unique id for the request. (If not provided, the SDK will generate a random identifier for your application)
	 * @param Ad_Presentation|null $adPresentation (optional) the ad format type
	 * @param array|null $keywords (optional) array of strings. Strings with the keywords the ads are related to .
	 * @param Protection_Policy|null $protectionPolicy (optional) the adult control policy. It will be safe, low, high. 
	 * @param String|null $userAgent (optional) the user agent of the client
	 * @param string|null $country (optional) country where the target user is located. Must follow ISO-3166 (see http://www.iso.org/iso/country_codes.htm).
	 * @return Ad_Response. The result returned by the server that contains the ad meta-data.
	 * @throws Bluevia_Exception
	 */
	public function getAdvertising(	$adSpace, $adRequestId = null,$adPresentation = null,$keywords = null,
	$protectionPolicy = null,$userAgent = null,$country=null){
		return parent::getAdvertising($adSpace, $adRequestId,$adPresentation, $keywords,$protectionPolicy,
		$userAgent, $country);
	}

	/**
	 * Requests the retrieving of an advertisement.
	 * This functions can only be used in 2-legged-mode (OAuth token should not have been included in the client's construction)
	 *
	 * @param string $adSpace the adSpace of the Bluevia application
	 * @param string $country country where the target user is located. Must follow ISO-3166 (see http://www.iso.org/iso/country_codes.htm).
	 * @param string|null $targetUserId (optional) Identifier of the Target User (optional).
	 * @param string|null $adRequestId (optional) an unique id for the request. (If not provided, the SDK will generate a random identifier for your application)
	 * @param Ad_Presentation|null $adPresentation (optional) the ad format type
	 * @param array|null $keywords (optional) array of string. Strings with the keywords the ads are related to.
	 * @param Protection_Policy|null $protectionPolicy (optional) the adult control policy. It will be safe, low, high. 
	 * @param String|null $userAgent (optional) the user agent of the client
	 * @return Ad_Response. The result returned by the server that contains the ad meta-data
	 * @throws Bluevia_Exception
	 */
	public function getAdvertising2L( $adSpace, $country,$targetUserId = null,$adRequestId = null,
	$adPresentation = null,$keywords = null,$protectionPolicy = null,$userAgent = null){
		Utils::checkParameter(array('$country'=>$country));
		return parent::getAdvertising2L( $adSpace, $country,null,$targetUserId,$adRequestId,
		$adPresentation,$keywords,$protectionPolicy,$userAgent);
	}


}