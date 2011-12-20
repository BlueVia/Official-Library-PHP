<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2011 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */

/**
 * This class provides access to the set of functions which any user could use to access the Bluevia Payment Service functionality
 */
class BlueviaClient_Api_Payment extends BlueviaClient_Api_Client_RPC {
    /**
    * Helper to determine current class' API Name
    * @return <string> API Name
    */
    protected function _getAPIName() {
        return 'Payment';
    }

    
    const TRANSACTION_STATUS_SUCCESS = 'Success';
    const TRANSACTION_STATUS_FAILURE = 'Failure';

	/**	 
	 * Creates a PaymentClient object to be able to do payments through the gSDP
	 * @param BlueviaClient $unica
	 */
    public function  __construct(BlueviaClient $unica) {
     
        $valid_statuses = array(
            self::TRANSACTION_STATUS_SUCCESS,
            self::TRANSACTION_STATUS_FAILURE
        );
        
        parent::__construct($unica);
    }
    
    /**           
     * Gets the RequestToken as first step of the OAuth process in order to make a payment operation.
     * @param int $amount Amount to be charged, it may be an economic amount or a number of 'virtual units' (points, tickets, etc) (mandatory).
     * @param string $currency Type of currency which corresponds with the amount above, following ISO 4217 (mandatory).
     * @param string $serviceID
     * @param string $serviceName
     * @param string $callback_url
     * @param bool $autoredirect
     * @return Zend_Oauth_Token_Request The RequestToken
     * @throws BlueviaClient_Exception_Parameters
     * @throws BlueviaClient_Exception_Client
     */
    public function getRequestToken($amount,
    								$currency,
    								$serviceID,
    								$serviceName,						
    								$callback_url = null,
    								$autoredirect = true) {    									
    	if((!is_integer($amount)) || ($amount < 0))
    		throw new BlueviaClient_Exception_Parameters("Wrong amount value");
    	if ((is_null($currency)) || (empty($currency)))
    		throw new BlueviaClient_Exception_Parameters("Wrong currency value. It cannot be empty");
    	
    	$oAuth = $this->_unica->getService('Oauth');
    	try {    		    		
    		$context = $this->_unica->getContext();
    		$environment = $this->_unica->offsetGet("environment");
    		$oauth_Name = "Payment".$environment;
    		$custom_payment_options = array();
			$custom_payment_options['paymentInfo.amount'] = $amount;
			$custom_payment_options['paymentInfo.currency'] = $currency; 
			$custom_payment_options['serviceInfo.serviceID'] = $serviceID;
			$custom_payment_options['serviceInfo.name'] = $serviceName;
    		$requestToken = $oAuth->getRequestToken($context["app"]["consumer_key"],
    												$context["app"]["consumer_secret"],
    												$callback_url,
    												$autoredirect,
    												$custom_payment_options,
    												$oauth_Name);
			unset($oAuth);    											
			if(!$autoredirect || empty($callback_url))	
				return $requestToken;    											
    	} catch (Exception $ex) {
    		unset($oAuth);
    		throw new BlueviaClient_Exception_Client($ex->getMessage());
    	}
    }
    
    /**
     * Gets the AccessToken.
     * Additionally the client stores the access token for previous payment operations.
     * @param string $oauth_verifier The code returned by oAuth Application
     * @param string $request_token If the application did not the manual redirection, the
     *                  request token object with Zend_Oauth_Token_Request type
     *                  returned by the getRequestToken function must 
     *                  be provided here @see getRequestToken from OAuth.php    
     * 
     * @return array ('REQUEST_TOKEN', 'ACCESS_TOKEN')     
     */
    public function getAccessToken($oauth_verifier,$request_token = null) {
    	if ((is_null($oauth_verifier)) || (empty($oauth_verifier)))
    		throw new BlueviaClient_Exception_Parameters("Wrong oauth_verifier value. It cannot be empty");
    	$oAuth = $this->_unica->getService('Oauth');
    	try {
    		$context = $this->_unica->getContext();
    		$access_token = $oAuth->getAccessToken($oauth_verifier,
    											$context["app"]["consumer_key"],
    											$context["app"]["consumer_secret"],
    											$request_token);
  			unset($oAuth);  			
  			$this->_setToken($access_token['ACCESS_TOKEN']->getToken(), $access_token['ACCESS_TOKEN']->getTokenSecret());
    		return $access_token;
    	} catch (Exception $ex) {
    		unset($oAuth);
    		throw new BlueviaClient_Exception_Client($ex->getMessage());
    	}
    }

    /**
     * Allows to request a charge to the account indicated by the end user identifier          
     * @param int $amount Amount to be charged, it may be an economic amount or a number of 'virtual units' (points, tickets, etc) (mandatory).
     * @param string $currency Type of currency which corresponds with the amount above, following ISO 4217 (mandatory).
     * @return array ('transactionId','transactionStatus','transactionStatusDescription') or Error
     */
    public function payment(                                   
            $amount,
            $currency,            
			$endpoint = null,
			$correlator = null
            ) {
        
        $method = 'PAYMENT';
        $timestamp = time();        
        $datetime = date("Y-m-d H:m:s", $timestamp);
        $datetime = str_replace(" ","T",$datetime);
        $datetime .="Z";   
        $params = array(            
                'ns2:timestamp' => $datetime,
                'ns2:paymentInfo' => array(
                    'ns2:amount' => $amount,
                    'ns2:currency' => $currency,
                )                
        );       
        if((!is_null($endpoint)) && (!is_null($correlator))) 
        	$params['ns2:receiptRequest'] = array(
                	'ns3:endpoint' => $endpoint,
                	'ns3:correlator' => $correlator
                );               
        $params = array('ns2:paymentParams' => $params);
        $header_mods = array('oauth_timestamp' => $timestamp);
		try {                
        	$result = $this->_RPCRequest('/payment',$method, $params,$header_mods);
        	$result = $this->_simplifyResponse($result,
                								array('ns1:transactionid', 
                									'ns1:transactionstatus', 
                									'ns1:transactionstatusdescription'));
			$end_result['transactionId'] = $result['ns1:transactionid'];
			$end_result['transactionStatus'] = $result['ns1:transactionstatus'];
			$end_result['transactionStatusDescription'] = $result['ns1:transactionstatusdescription'];			            								
		} catch(Exception $ex) {
			$end_result = $this->_lasterror;
		}        
        
        return $end_result;        
        
    }
    
    /**
     * The function getPaymentStatus allows to request the status of a previous payment operation
     * @param string $transactionId Id of the previous payment operation
     * @return array ('transactionStatus','transactionStatusDescription')
     */
    public function getPaymentStatus($transactionId) {
    	if((is_null($transactionId)) || empty($transactionId))
    		throw new BlueviaClient_Exception_Parameters("TransactionId cannot be empty or null");
    	$method = 'GET_PAYMENT_STATUS';
    	$params = array('ns2:transactionId' => $transactionId);
    	$params = array('ns2:getPaymentStatusParams' => $params);
    	try{
    		$result = $this->_RPCRequest('/getPaymentStatus',$method,$params);
    		$result = $this->_simplifyResponse($result,
                								array( 'ns1:transactionstatus', 
                									'ns1:transactionstatusdescription'));
			$end_result['transactionStatus'] = $result['ns1:transactionstatus'];
			$end_result['transactionStatusDescription'] = $result['ns1:transactionstatusdescription'];						                								
    	} catch(Exception $ex) {
    		$end_result = $this->_lasterror;
    	}
    	return $end_result;
    }

    /**
     * Merchant can use this operation instead of payment,
     * to cancel a previously authorized purchase.
     */
    public function cancelAuthorization() {                
        $method = 'CANCEL_AUTHORIZATION';
        try {
        	$result = $this->_RPCRequest('/cancelAuthorization',$method);
        } catch (Exception $ex) {
        	$result = $this->_lastError;
        }         
        //@todo: should return something?
        return $result;
    }
     

    /**
     * Generates an RPC Request: Overriden parent method for setting endpoint
     * @param <type> $method
     * @param <type> $params
     * @return <type>
     */
    protected function _RPCRequest($function_uri,$method, $params = null, $header_mods = null) {
    	$uri = '/RPC/'.$this->_getAPIName().$this->_unica->offsetGet("environment").$function_uri;
        return parent::_RPCRequest(
                $uri,
                $method,
                $params,
                $header_mods
        );
    }
        
    /**
     * @var Zend_XmlRpc_Client
     */
    protected $_client;

    protected $valid_statuses;
}