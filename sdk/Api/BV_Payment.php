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
 * Client interface for the REST binding of the Bluevia Payment Service.
 *
 * @author Telefonica R&D
 *
 */
class BV_Payment extends BV_OAuth_Client{

	/**
	 * URLEncoded_Parser $_urlEncParser URL Encoded parser to parse the HTTP's response body
	 */
	private $_urlEncParser;
	/**
	 * URLEncoded_Serializer $_urlEncSerializer URL Encoded serializer to serialize the HTTP's request body
	 */
	private $_urlEncSerializer;
	/**
	 * RPC_Parser $_rpcParser RPC parser to parse the HTTP's response body
	 */
	private $_rpcParser;
	/**
	 * RPC_Serializer $_rpcSerializer RPC serializer to serialize the HTTP's request body
	 */
	private $_rpcSerializer;
	/**
	 * XML_Parser $_xmlParser XML parser to parse the HTTP's response body
	 */
	private $_xmlParser;
	/**
	 * Json_Parser $_jsonParser Json parser to parse the HTTP's response body
	 */
	private $_jsonParser;

	/**
	 * Payment API constructor
	 * @param BV_Mode $mode BlueVia provides three modes to support the different
	 * 						development stages of your app. [LIVE,TEST,SANDBOX]
	 * @param String $consumer The consumer key
	 * @param String $consumerSecret The cosumer secret
	 * @param String|null $token (Optional). The access token
	 * @param String|null $tokenSecret (Optional)
	 * @throws Bluevia_Exception
	 */
	public function __construct($mode,$consumer,$consumerSecret,$token=null,$tokenSecret=null){
		if ($mode==BV_Mode::TEST){
			throw new Bluevia_Exception('-7',null,'Payment ',' LIVE and SANDBOX modes.');
		}
		$this->checkTwoLeggedCredentials($consumer,$consumerSecret);
		parent::initUntrusted($mode,$consumer,$consumerSecret,$token,$tokenSecret);
		$this->setParameters();

	}

	/**
	 * Gets a RequestToken for a Payment operation
	 *
	 * @param int $amount  the cost of the digital good being sold, expressed in the minimum fractional monetary unit of the currency reflected in the next parameter (to avoid decimal digits).
	 * @param String $currency the currency of the payment, following ISO 4217 (EUR, GBP, MXN, etc.).
	 * @param String $serviceName the name of the service for the payment
	 * @param String $serviceId the id of the service for the payment
	 * @param String|null $callback (Optional) the callback to redirect the application once the request token has been authorized
	 * @param boolean $autoRedirect (Optional) True if create's token cookie
	 *                               and redirect's automatically to oAuth
	 *                               portal. Default to false
	 * @return Request_Token the request token
	 * @throws Bluevia_Exception
	 */
	public function getPaymentRequesttoken ($amount,$currency,$name,$serviceID,$callback=null,$autoRedirect=true){
		Utils::checkParameter(array('$currency'=>$currency,'$name'=>$name,'$serviceID'=>$serviceID));
		$callback=$this->checkCallbackUrl($callback);
		Utils::isInteger($amount,'$amount');
		$this->_parser=&$this->_urlEncParser;
		$this->_serializer=&$this->_urlEncSerializer;
		$paymentParams = array();
		$paymentParams['paymentInfo.amount'] = $amount;
		$paymentParams['paymentInfo.currency'] = $currency;
		$paymentParams['serviceInfo.serviceID'] = $serviceID;
		$paymentParams['serviceInfo.name'] = $name;
		$apiName='Payment';
		if ($this->_mode==BV_Mode::SANDBOX){
			$apiName.='_'.BV_Mode::SANDBOX;
		}
		$this->_connector->setExtraParameters(array(
										'xoauth_apiName'=>$apiName));
		try{
			$response=$this->baseCreate(BV_Constants::BASE_URL.BV_Constants::REST_URL.BV_Constants::OAUTH_URL.BV_Constants::REQ_URL,null,$paymentParams,'Content-type: '.BV_Constants::URL_ENCODED);
			return $this->createRequestTokenResponse($response,$callback,$autoRedirect);
		}catch (Connector_Exception $e){
			$this->_parseError($e,$this->_xmlParser);
		}
	}

	/**
	 * Gets an access token for a Payment operation
	 *
	 * @param String $oauthVerifier the OAuth verifier for the token
	 * @param String $token (Optional) the request token received previously
	 * @param String $secret (Optional) the request token secret
	 * @return Token the access token
	 * @throws Bluevia_Exception
	 */
	public function getPaymentAccessToken(
	$oauthVerifier,
	$requestToken = null, $requestSecret = null) {
		$this->_parser=$this->_urlEncParser;
		$this->checkAccessParams($oauthVerifier,$requestToken,$requestSecret);
		try{
			$response=$this->baseCreate(BV_Constants::BASE_URL.BV_Constants::REST_URL.BV_Constants::OAUTH_URL.BV_Constants::ACC_URL);
			$token=$this->simplifyResponse(new Token(),$response);
			$this->_connector->setTokens($token->key,$token->secret);
			return $token;
			// is responsability of the application to mantain Token + Token Secret
		}catch (Connector_Exception $e){
			$this->_parseError($e,$this->_xmlParser);
		}
	}

	/**
	 Sets the access token of the session
	 * This functions is used to change the token of the session to be able to get the
	 * payment status of an old operation, or cancel the authorization of a token.
	 * @param String $token
	 * @param String $tokenSecret
	 * @throws Bluevia_Exception
	 */
	public function setToken($token,$tokenSecret){
		$this->_connector->setTokens($response->key,$response->secret);
	}
	/**
	 * Allows to request a charge to the account indicated by the end user identifier
	 * @param int $amount Amount to be charged, it may be an economic amount or a number of 'virtual units' (points, tickets, etc) (mandatory).
	 * @param String $currency Type of currency which corresponds with the amount above, following ISO 4217 (mandatory).
	 * @param String $endpoint (Optional) the endpoint to receive notifications of the payment operation
	 * @param String $correlator (Optional) the correlator.
	 * @return Payment_Result Result of the payment operation.
	 * @throws Bluevia_Exception
	 */
	public function payment(
	$amount,
	$currency,
	$endpoint = null,
	$correlator = null
	) {
		Utils::checkParameter(array('$currency'=>$currency));
		Utils::isInteger($amount,'$amount');
		$this->_checkEndpoint($endpoint,$correlator);
		$timestamp=time();
		$datetime= Utils::createDateTime($timestamp);
		$rpcParams=array('paymentParams'=>
		array('paymentInfo'=>array('amount'=>$amount,'currency'=>$currency),'timestamp'=>"$datetime")
		);
		$rpcParams['method']='PAYMENT';
		$this->_connector->setExtraParameters(array('oauth_timestamp'=>$timestamp));
		$endpoint=$this->_checkEndpoint($endpoint,$correlator);
		if (!empty($endpoint)){
			$body['receiptRequest']=$endpoint;
		}
		$this->_serializer=&$this->_rpcSerializer;
		$this->_parser=&$this->_rpcParser;
		$url=$this->_rewriteUrl(BV_Constants::RPC_URL.BV_Constants::PAY_URL.BV_Constants::PAY_PAY_URL);
		$response=$this->baseCreate($url,null,$rpcParams,'Content-type: '.BV_Constants::JSON);
		return $this->_createInfo($response->paymentResult, new Payment_Result());

	}

	/**
	 * Retrieves the status of a previous payment operation
	 *
	 * @param String $transactionId the id of the transaction
	 * @return Payment_Status_Result the status of the payment
	 * @throws Bluevia_Exception
	 */
	public function getPaymentStatus ($transactionId){
		Utils::checkParameter(array('$transactionId'=>$transactionId));
		$rpcParams=array('getPaymentStatusParams'=>array('transactionId'=>$transactionId));
		$rpcParams['method']='GET_PAYMENT_STATUS';
		$this->_serializer=&$this->_rpcSerializer;
		$this->_parser=&$this->_rpcParser;
		try{
			$url=$this->_rewriteUrl(BV_Constants::RPC_URL.BV_Constants::PAY_URL.BV_Constants::PAY_STA_URL);
			$response=$this->baseCreate($url,null,$rpcParams,
			'Content-type: '.BV_Constants::JSON);
			return $this->_createInfo($response->getPaymentStatusResult,
			new Payment_Status_Result());
		} catch ( Exception $e){
			$this->_parseError($e,$this->_jsonParser);

		}
	}

	/**
	 * Merchant can use this operation to invalidate the access token of the session.
	 * If the payment has been made before, it remains valid, but no more getPaymentStatus operations will be enabled.
	 * @return boolean True if the payment authorization was cancelled succesfully.
	 * @throws Bluevia_Exception.
	 */
	public function cancelAuthorization(){
		$rpcParams['method']='CANCEL_AUTHORIZATION';
		$this->_serializer=&$this->_rpcSerializer;
		try{
			$url=$this->_rewriteUrl(BV_Constants::RPC_URL.BV_Constants::PAY_URL.BV_Constants::PAY_CAN_URL);
			$response=$this->baseCreate($url,null,$rpcParams,'Content-type: '.BV_Constants::JSON);
			return true;
		} catch ( Exception $e){
			$this->_parseError($e,$this->_rpcParser);

		}
	}

	/**
	 * Helper function for setting the Client properties
	 */
	protected function setParameters(){
		$this->_urlEncSerializer=new URLEncoded_Serializer();
		$this->_urlEncParser= new URLEncoded_Parser();
		$this->_rpcParser= new RPC_Parser();
		$this->_rpcSerializer= new RPC_Serializer();
		$this->_xmlParser= new XML_Parser();
		$this->_jsonParser= new Json_Parser();
	}

}
