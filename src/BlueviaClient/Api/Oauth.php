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
 * Include Zend Oauth Library: warning needs to be in include_path!
 * @see http://oauth.net
 * @see http://framework.zend.com/
 */
include_once 'Zend/Oauth/Consumer.php';

/** Zend_Oauth_Http_RequestToken */
require_once 'Zend/Oauth/Http/RequestToken.php';


class BlueviaClient_Zend_OAuth_Http_RequestToken extends Zend_OAuth_Http_RequestToken 
{
	
	protected $_custom_header;
	
	public function setCustomHeader($custom_header) {
		if(is_array($custom_header))
			$this->_custom_header = $custom_header;
	}
	
	/**
     * Assemble all parameters for an OAuth Request Token request.
     *
     * @return array
     */
    public function assembleParams()
    {
        $params = array(
            'oauth_consumer_key'     => $this->_consumer->getConsumerKey(),
            'oauth_nonce'            => $this->_httpUtility->generateNonce(),
            'oauth_timestamp'        => $this->_httpUtility->generateTimestamp(),
            'oauth_signature_method' => $this->_consumer->getSignatureMethod(),
            'oauth_version'          => $this->_consumer->getVersion(),        	
        );
        
        	        
                
        // indicates we support 1.0a
        if ($this->_consumer->getCallbackUrl()) {
            $params['oauth_callback'] = $this->_consumer->getCallbackUrl();
        } else {
            $params['oauth_callback'] = 'oob';
        }
        
        if(isset($this->_custom_header) && (!is_null($this->_custom_header)))
	    	foreach($this->_custom_header as $key => $value)
	        	$params[$key] = $value;

        if (!empty($this->_parameters)) {
            $params = array_merge($params, $this->_parameters);
        }

        $params['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $this->_consumer->getSignatureMethod(),
            $this->_consumer->getConsumerSecret(),
            null,
            $this->_preferredRequestMethod,
            $this->_consumer->getRequestTokenUrl()
        );

        return $params;
    }
	

	/**
     * Generate and return a HTTP Client configured for the Header Request Scheme
     * specified by OAuth, for use in requesting a Request Token.
     *
     * @param array $params
     * @return Zend_Http_Client
     */
    public function getRequestSchemeHeaderClient(array $params)
    {   
    	foreach ($params as $key => $value) {
    		if(isset($this->_custom_header[$key])) {            
	        	if(!is_null($this->_custom_header[$key]))
	        		continue;	          
    		}
    		$params_aux[$key] = $value;
    	}
        $headerValue = $this->_httpUtility->toAuthorizationHeader(
            $params_aux
        );
        if(isset($this->_custom_header)) {
        	foreach($this->_custom_header as $key => $value) {
        		$headerValue.= ",".$key."=\"".$value."\"";
        	}
        }
        $client = Zend_Oauth::getHttpClient();
        $client->setUri($this->_consumer->getRequestTokenUrl());
        $client->setHeaders('Authorization', $headerValue);
        if(isset($this->_custom_header)) {
        	$client->setRawData('', '');
        	$rawdata = $this->_httpUtility->toEncodedQueryString($params_aux, true);
        	if (!empty($rawdata)) {
            	$client->setRawData($rawdata, 'application/x-www-form-urlencoded');
        	}        	
        } else {        	
        	$client->setRawData('', '');
        }
        $client->setMethod($this->_preferredRequestMethod);
        return $client;
    }
	
}
/**
 * oAuth Helper class
 */
class BlueviaClient_Api_Oauth extends BlueviaClient_Api_Client_Base
{

    /** @var Zend_Oauth_Consumer **/
    protected $_oauth = null;
    
    /**
    * Helper to determine current class' API Name
    * @return string API Name
    */
    protected function _getAPIName() {
        return 'Oauth';
    }


    /**
     * Obtain request token
     * @param  string  $callback_url Current Application URL or MSISDN for the SMS handshake
     * @param  boolean $auto_redirect Creates token cookie
     *                               and redirects automatically to oAuth
     *                               portal (BlueviaClient_Api_Constants::$oauth_url)
     * @param array $custom Custom oauth parameters 
     * @param string $apiName API name for which you are requesting access
     * @return array(Zend_Oauth_Token_Request, oauth_url     
     * @throws Zend_Oauth_Exception,
     * BlueviaClient_Exception_Parameters,
     * BlueviaClient_Exception_Response
     */
    public function getRequestToken(
            $callback_url = null,
            $auto_redirect = true,
            $custom = null,
            $apiName = null) {
		
        // Get consumer key and consumer secret
        $context=$this->_unica->getContext();
        $consumer_key=$context['app']['consumer_key'];
        $consumer_secret=$context['app']['consumer_secret'];
        
        // check required parameters
        $this->_checkParameter(
                array($consumer_key, $consumer_secret),
                "Consumer Key and Consumer Secret cannot be null."
                );

		$oauthUrl=$this->_unica->offsetGet('oauthUrl');
		
        // check valid environment for SMSHANDSHAKE
        if (is_numeric($callback_url) && $this->_unica->offsetGet('oauthUrl')==BlueviaClient_Api_Constants::OAUTH_URL_TEST){      
        	throw new BlueviaClient_Exception_Client('SMSHANDSHAKE only valid with LIVE environment');}
        	
        if (Zend_Uri::check($callback_url) || is_null($callback_url) ){
        // Obtain array of options, structured for Zend oAuth Library
        $oauthOptions = self::_get_options(
                $consumer_key,
                $consumer_secret,
                $callback_url                                           
                );}
        else if (empty($callback_url) || $callback_url ==='oob' || is_numeric($callback_url)){
        $oauthOptions = self::_get_options(
                $consumer_key,
                $consumer_secret,
                null                                           
                );}
        else{
        	throw new BlueviaClient_Exception_Parameters('ERROR! $callback_url must be an URL, MSISDN or null');
        }
        
        $this->_oauth = new Zend_Oauth_Consumer($oauthOptions);
        $oauth = $this->_oauth;
        // configure certificates
        $client = $oauth->getHttpClient();                
        
        $this->_setClientConfig($client);
        $oauth->setHttpClient($client);
        
        // obtain request Token.
        $request = new BlueviaClient_Zend_OAuth_Http_RequestToken($oauth, $custom);
        if(!is_null($apiName))
        	$custom_header = array ( "xoauth_apiName" => $apiName);
        	
        // check MSISDN for the SMS handshake
        if(!is_null($callback_url) && is_numeric($callback_url))
        	$custom_header['oauth_callback']=$callback_url;
       	if((isset($custom_header)) && (!is_null($custom_header)))	
			$request->setCustomHeader($custom_header);     
        $token = $oauth->getRequestToken($custom,null,$request); 
               
        
        // redirect to oAuth Portal, allowing the user to authorize app
        //if ($auto_redirect && !empty($callback_url)) {
        if ($auto_redirect && Zend_Uri::check($callback_url)) {
            // store token: we will need later for getAccessToken Method
            // Cookie support is required!
            $set = setcookie('req_token', serialize($token), null, '/');
            $oauth->redirect();
            
        } else {
        	$requestToken_response= array ('token' => $token,
        									'oauth_url' => BlueviaClient_Api_Constants::$oauth_url.'?oauth_token='.$token->getToken());
            return $requestToken_response;
        }
    }

    /**
     * Get Access Token
     * @param string $oauth_verifier The code returned by oAuth Application
     * @param string $request_token If application did manual redirection, the
     *                  request token object with Zend_Oauth_Token_Request type
     *                  returned by the getRequestToken function must 
     *                  be provided here @see getRequestToken    
     * 
     * @return array ('REQUEST_TOKEN', 'ACCESS_TOKEN')
     * @throws BlueviaClient_Exception_Parameters
     */
    public function getAccessToken(
            $oauth_verifier,
            $request_token = null) {
        
         // get request token from parameters or from cookie: @see getRequestToken
        if (empty($request_token)) {
            $request_token = unserialize($_COOKIE['req_token']);
        }
        
         // Get consumer key and consumer secret
        $context=$this->_unica->getContext();
        $consumer_key=$context['app']['consumer_key'];
        $consumer_secret=$context['app']['consumer_secret'];
        
        // check required parameters
        $this->_checkParameter(
                array($oauth_verifier, $consumer_key, $consumer_secret),
                "OAuth Verifier and Consumer parameters cannot be null."
                );
                
        // Obtain array of options, structured for Zend oAuth Library
        $oauthOptions = self::_get_options($consumer_key, $consumer_secret);

        // get oAuth consumer object
        $this->_oauth = new Zend_Oauth_Consumer($oauthOptions);
        $oauth = $this->_oauth;

        // configure certificates
        $client = $oauth->getHttpClient();
        $this->_setClientConfig($client);
        $oauth->setHttpClient($client);
        
        // if already empty...
        if (empty($request_token)) {
            throw new BlueviaClient_Exception_Parameters('ERROR! Request token cookie
                is missing, please call getRequestToken before asking
                for access token'
                    );
        }
        
        // for console authorisation
        if (empty($_GET['oauth_verifier'])) {
            $_GET['oauth_verifier'] = $oauth_verifier;
        }

        if (empty($_GET['oauth_token'])) {
            $_GET['oauth_token'] = $request_token->getToken();
        }

        // request user Access Token
        $access_token = $oauth->getAccessToken($_GET,
                                $request_token);

        // is responsability of the application to mantain Token + Token Secret
        return array(
            'REQUEST_TOKEN'=>$request_token,
            'ACCESS_TOKEN'=>$access_token,
            'HTTP_CLIENT'=>$oauth->getHttpClient()
        );
    }
   


    /**
     * Returns a valid HTTP Client to be used with APIs integrated among oAuth     
     * @param array $application_context user{token_access, token_secret},
     *                                     app{consumer_key, consumer_secret}
     * 
     * @return Zend_Http_Client_Adapter_Curl Zend HTTP Client object
     */
    public function getHttpClient($application_context) {
        $token = new Zend_Oauth_Token_Access();


        $oauthOptions = self::_get_options(
                $application_context['app']['consumer_key'],
                $application_context['app']['consumer_secret']
        );

        if (!empty($application_context['user']['token_access'])) {
            $token->setToken($application_context['user']['token_access']);
        }

        if (!empty($application_context['user']['token_secret'])) {
            $token->setTokenSecret($application_context['user']['token_secret']);
        }
        //@todo: verify 2-legged
        
        // no need for these urls, save some memory
        unset($oauthOptions['accessTokenUrl']);
        unset($oauthOptions['requestTokenUrl']);

        // obtain HTTP Client, with oAuth header always integrated        
        $client = $token->getHttpClient($oauthOptions);

        $client->setAdapter('Zend_Http_Client_Adapter_Curl');

        //***********ONLY if Curl support is enabled***************************
        $this->_setClientConfig($client);
                
        
        return $client;
    }


    /**
     * Return constants for oAuth
     *
     * @param string $consumerKey The CONSUMER KEY
     * @param string $consumerKey The CONSUMER SECRET
     * @param string $callBackUrl The callbackUrl
     * 
     * @return array The array of options
     */
    protected function _get_options($consumerKey, $consumerSecret, $callbackUrl = null) {
        $options =  array(
            'consumerKey'=> $consumerKey,
            'consumerSecret'=> $consumerSecret,
            'siteUrl'       => BlueviaClient_Api_Constants::$oauth_url,
        	'authorizeUrl' => BlueviaClient_Api_Constants::$oauth_url,
            'accessTokenUrl'=> $this->_unica->composeUrl('/REST/Oauth/getAccessToken'),
            'requestTokenUrl'=> $this->_unica->composeUrl('/REST/Oauth/getRequestToken'),
            'signatureMethod' => 'HMAC-SHA1',
        );
        // add callback url if present to requests
        if (!empty($callbackUrl)) {
            $options['callbackUrl'] = $callbackUrl;
        }
        return $options;
    }

    /**
     * Setups CURL Configuration
     * @param Zend_Http_Client $client
     * 
     */
    protected function _setClientConfig(&$client) {
        $client->setAdapter('Zend_Http_Client_Adapter_Curl');
        $client->setConfig(
                array(
                    'curloptions' => array(
                      // CA Authority base cert
                      CURLOPT_CAINFO   => dirname(__FILE__) . DIRECTORY_SEPARATOR. "certificate.crt",
                      CURLOPT_SSL_VERIFYHOST => 2,
                      CURLOPT_SSL_VERIFYPEER => FALSE,
                      CURLOPT_FAILONERROR=> FALSE                    
                    )
                )
        );
        
    }
    
}

