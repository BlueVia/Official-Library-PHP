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
 * Abstract client for the REST binding of the Bluevia Directory Service.
 *
 * @author Telefonica R&D
 *
 */
abstract class BV_Advertising_Client extends BV_Base_Client{

	/**
	 * XML_Parser $_errorParser Parser used when error.
	 */
	private $_errorParser;


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
	protected function getAdvertising($adSpace, $adRequestId = null,
	$adPresentation = null,
	$keywords = null,
	$protectionPolicy = null,
	$userAgent = null,
	$country=null){

		if ($this->_connector->isTwoLegged()){
			throw new Bluevia_Exception('-110',null,'getAdvertising2L()','an advertising
									with a two legged client');

		}
		$adPresentation=Ad_Presentation::getValue($adPresentation);
		$protectionPolicy=Protection_Policy::getValue($protectionPolicy);
		Utils::checkParameter(array('$adSpace'=>$adSpace));
		
		if (empty($userAgent) || $userAgent===''){
			$userAgent='none';
		}

		// if ad_request_id param is empty it is composed by the library
		if (empty($adRequestId)){
			$adRequestId = $this->_getComposedAdRequestId($adRequestId);
		}

		$requiredParams=array('ad_space'=>$adSpace,'ad_request_id'=>$adRequestId,
								'user_agent'=>$userAgent);
		$params=array('country'=>$country,'ad_presentation'=>$adPresentation,
						'keyword'=>$this->_composeKeyword($keywords),'protection_policy'=>$protectionPolicy);
		$requiredParams=$this->_addParameters($params,$requiredParams);

		try{
			$response= $this->baseCreate(null,null,$requiredParams,'Content-type: '.BV_Constants::URL_ENCODED);
			return $this->_simplifyAdresponse($response,$adRequestId);
		}
		catch (Connector_Exception $e){
			$this->_parseError($e,$this->_errorParser);
		}
	}

	/**
	 * Requests the retrieving of an advertisement.
	 * This functions can only be used in 2-legged-mode (OAuth token should not have been included in the client's construction)
	 *
	 * @param string $adSpace the adSpace of the Bluevia application
	 * @param string $country (optional) Country where the target user is located. Must follow ISO-3166 (see http://www.iso.org/iso/country_codes.htm).
	 * @param string $phoneNumber (optional) Customer's phone number to whom the Advertising is targeted.
	 * @param string|null $targetUserId (optional) Identifier of the Target User (optional).
	 * @param string|null $adRequestId (optional) an unique id for the request. (If not provided, the SDK will generate a random identifier for your application)
	 * @param Ad_Presentation|null $adPresentation (optional) the ad format type
	 * @param array|null $keywords (optional) array of string. Strings with the keywords the ads are related to.
	 * @param Protection_Policy|null $protectionPolicy (optional) the adult control policy. It will be safe, low, high. 
	 * @param String|null $userAgent (optional) the user agent of the client
	 * @return Ad_Response. The result returned by the server that contains the ad meta-data
	 * @throws Bluevia_Exception
	 */
	protected function getAdvertising2L( $adSpace, $country=null,
	$phoneNumber=null,
	$targetUserId = null,
	$adRequestId = null,
	$adPresentation = null,
	$keywords = null,
	$protectionPolicy = null,
	$userAgent = null){

		if (!$this->_connector->isTwoLegged()){
			throw new Bluevia_Exception('-110',null,'getAdvertising()',"an advertising with a three legged client");

		}
		$adPresentation=Ad_Presentation::getValue($adPresentation);
		$protectionPolicy=Protection_Policy::getValue($protectionPolicy);
		Utils::checkParameter(array('$ad_space'=>$adSpace));

		if (empty($userAgent) || $userAgent===''){
			$userAgent='none';
		}

		// if ad_request_id param is empty it is composed by the library
		if (empty($adRequestId)){
			$adRequestId = $this->_getComposedAdRequestId($adRequestId);
		}
		$requiredParams=array('ad_space'=>$adSpace,'ad_request_id'=>$adRequestId,
								'user_agent'=>$userAgent,'country'=>$country);
		$params=array('target_user_id'=>$targetUserId,'ad_presentation'=>$adPresentation,
						'keyword'=>$this->_composeKeyword($keywords),'protection_policy'=>$protectionPolicy);
		$requiredParams=$this->_addParameters($params,$requiredParams);
		$headers=array();
		if (!empty($phoneNumber)){
			Utils::isPhoneNumber($phoneNumber);
			$headers[]='X-PhoneNumber: '.$phoneNumber;
		}
		$headers[]='Content-type: '.BV_Constants::URL_ENCODED;
		try{
			$body= $this->baseCreate(null,null,$requiredParams,$headers);
			return $this->_simplifyAdresponse($body,$adRequestId);
		}
		catch (Connector_Exception $e){
			$this->_parseError($e,$this->_errorParser);
		}
	}
	
	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters (){
		$this->_errorParser= new Json_Parser();
		$this->_parser=new XML_Parser();
		$this->_serializer= new URLEncoded_Serializer();
		$this->_rewriteUrl(BV_Constants::REST_URL.BV_Constants::ADV_URL);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  PRIVATE FUNCTIONS
	//
	//////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Helper to generate a random Ad request ID
	 * @return String an unique advertising id.
	 */
	private function _getComposedAdRequestId($adSpace) {
		if(strlen($adSpace) > 10)
		$adRequestId = substr($adSpace, 0, 10);
		else
		$adRequestId = $adSpace;
		$adRequestId .= time();
		if(!$this->_connector->isTwoLegged())
		$adRequestId .= $this->_connector->getToken();
		return $adRequestId;
	}

	/**
	 * Helper function to simplify the response to the developer.
	 * @param stdClass $response object containing the server's response information.
	 * @param String $adRequestId a unique id for this advertising.
	 * @return Ad_Response Simplified response for the get advertising request.
	 */
	private function _simplifyAdResponse($response,$adRequestId){
		$adResponse= new Creative_Element();
		$creativeElement=(string)$response->ad->
		resource->creative_element->attributes()->type;
		$adResponse->type=$creativeElement;
		if ($creativeElement=='text' && $this->_mode==BV_Mode::SANDBOX){
			$adResponse->value=(string)$response->ad->
			resource->creative_element->attribute[1];
		}else{
			$adResponse->value=(string)$response->ad->
			resource->creative_element->attribute;
		}
		$adResponse->interaction=(string)$response->ad->
		resource->creative_element->interaction->attribute;
		return new Ad_Response($adRequestId,$adResponse);
	}

	/**
	 * Helper function to filter the $params array with only the not null ones.
	 * @param array $params Array containing all advertising parameters (null and not null ones)
	 * @param array $allParameters Array containing only the not null advertising parameters.
	 */
	private function _addParameters ($params,$allParameters){
		foreach ($params as $paramName => $value){
			if (!empty($value) || $value !=''){
				$allParameters[$paramName]=$value;
			}
		}
		return $allParameters;
	}

	/**
	 * Helper function to convert the keyword array in to a string contatenated by the '|' character.
	 * @param array $keywords The array introduced in the getAdvertising function.
	 * @return String Contains all the keywords concatenated by '|' character.
	 */
	private function _composeKeyword($keywords){
		if (!is_array($keywords)){
			$keywords=array($keywords);
		}
		$outputKeyword='';
		foreach ($keywords as $value){
			$outputKeyword.=$value;
			if (next($keywords)){
				$outputKeyword.='|';
			}
		}
		return $outputKeyword;
	}

}