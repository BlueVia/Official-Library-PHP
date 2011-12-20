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
 * 
 */


/**
 * Include full API Package files
 */

include_once 'BlueviaClient/Api/Client/Base.php';
include_once 'BlueviaClient/Api/Client/RPC.php';
include_once 'BlueviaClient/Schemas/UserIdType.php';

include_once 'BlueviaClient/Exception.php';
include_once 'BlueviaClient/Api/Constants.php';
include_once 'BlueviaClient/Api/Messaging.php';
include_once 'BlueviaClient/Api/Sms.php';
include_once 'BlueviaClient/Api/Directory.php';
include_once 'BlueviaClient/Api/Oauth.php';
include_once 'BlueviaClient/Api/Advertising.php';
include_once 'BlueviaClient/Api/Mms.php';
include_once 'BlueviaClient/Api/Location.php';
include_once 'BlueviaClient/Api/Payment.php';
include_once 'BlueviaClient/Api/Callmanagement.php';
/**
 * Include extra exception types
 */
include_once 'BlueviaClient/Exception/Parameters.php';
include_once 'BlueviaClient/Exception/Response.php';
include_once 'BlueviaClient/Exception/Client.php';
include_once 'BlueviaClient/Exception/Server.php';

/**
 * Schemas simplifying api calls
 */
include_once 'BlueviaClient/Schemas/UserIdType.php';

/**
 * IMPORTANT NOTE: This API uses PEAR XML_Serializer
 * pear install "channel://pear.php.net/XML_Serializer-0.20.2"
 * @see http://pear.php.net/manual/
 */
include_once "XML/Serializer.php";

/**
 * Entry point for SDK access. Use getService Method to retrieve the api instances
 */
class BlueviaClient implements ArrayAccess
{
    const NO_V1PARAM = 'no_call';
    /** @var Zend_Http_Client */
    protected $_http = null;
    protected $_options = array();
    protected $_access_token = null;
    protected $_context = null;

    /** @var Zend_Http_Response */
    protected $_lastResponse = null;
    
    /** @var Zend_Http_Request */
    protected $_lastRequest = null;
    
    /** @var Zend_Log */
    static protected $_log = null;


    /**
     *
     * @param <array> $application_context user{token_access, token_secret},
     *                                     app{consumer_key, consumer_secret}          
     * @param <string> $log Zend Log instance
     */
    public function  __construct($application_context = null, $log = null)
    {
        if (!empty($application_context)) {
            $this->_context  = $application_context;
            /*@var BlueviaClient_Api_Oauth $oauth*/
            $oauth = $this->getService('Oauth');
            $this->_http = $oauth->getHttpClient($application_context);

            if (!empty($application_context['user']['token_access'])) {
                $this->_access_token = $application_context['user']['token_access'];
            }
        } else {
            $this->_http = null;
        }

        $this->_options['environment'] = BlueviaClient_Api_Constants::$environment;
        $this->_options['baseUrl']     = BlueviaClient_Api_Constants::$base_url;
        //$this->setOptions($options);
    }

    /**
     * If Log instance is present, writes to log file
     * @param <string> "info" or "err"
     * @param <string> $message
     */
    protected function watchdog($type, $message) {
        if(!empty($_log))  {
            switch($type) {
                case 'info':
                    self::$_log->info($message);
                    break;
                case 'err':
                    self::$_log->err($message);
                    break;
            }
            return true;
        } else {
            // log is not enabled
            return false;
        }

    }

    /**
     * Helper for APIS obtaining access token without the need of 
     * asking it to user
     */
    public function getAccessToken() {        
        return $this->_access_token;
    }
    
    /**
     * Obtain Http Client
     * @return Zend_Http_Client    
     */

    public function getHttpClient()
    {
        return $this->_http;
    }
    
	/**
	 * 
	 * Obtain context containing app and user data
	 * @return array
	 */
	public function getContext() {
		return $this->_context;
	}
	
    /**
     * Sets the http client 
     * @param <Zend_Http_Client> $http
     */
    public function setHttpClient($http)
    {
        $this->_http = $http;
    }

    /**
     * Sets base Url (if different from connect.bluevia.com)
     * @param <string> $url
     */
    public function setBaseUrl($url)
    {
        $this->_options['baseUrl'] = $url;
    }

    /**
     * Generic function to obtain an API By name
     * @param <string> $name BlueviaClient_Api_XXX
     * @return instance of BlueviaClient_Api_XXX object
     */
    public function getService($name)
    {
        $class = 'BlueviaClient_Api_' . ucfirst(strtolower($name));
        
        if(!class_exists($class)) {
            throw new BlueviaClient_Exception_Parameters(
                    "The specified API is not supported"
            );
        }

        // two-legged authorization: Apis that allow skip access token connect auth
        $twolegged_classes = array(
            'BlueviaClient_Api_Advertising',
            'BlueviaClient_Api_Oauth',
        	'BlueviaClient_Api_Payment'
        );

        if (!in_array($class, $twolegged_classes)) {
            if (empty($this->_context['user']['token_access']) ||
                    empty($this->_context['user']['token_secret'])) {
                throw new BlueviaClient_Exception_Parameters("Missing token information");
            }
        }

        return new $class($this);        
    }
    
    /**
     * Obtain last response object
     * @ignore
     * @return <Zend_Http_Response>
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }
    
    /**
     * Obtain last request object
     * @ignore
     * @return <Zend_Http_Request>
     */    
	public function getLastRequest()
    {
    	return $this->_lastRequest;
    }

    /**
     * Replaces the %ENV% token by the selected (Sandbox, Commercial)
     * @ignore
     * @return <string>
     */
    protected function _rewriteUrl($url)
    {
        return str_replace('%ENV%', $this['environment'], $url);
    }

    public function composeUrl($url, $add_env = false)
    {
        if ($add_env) {
            $url = $this->_rewriteUrl($url);
        }
        
        $url = rtrim($this['baseUrl'], '/') . $url;
        
        return $url;
    }


    /**
     * 
     * doRequest: for internal usage     
     * @param <string> $method GET/POST/DELETE
     * @param <string> $url    Relative URL
     * @param <array|object|string> $body   Body Data
     * @param <array> $files  Attached files
     * @param <string> $encoding encoding
     * @param <string> $query Query params
     * @return HTTP Message (200, ...)
     */
    public function doRequest(
            $method,
            $url,
            $body = '',
            $files = array(),
            $encoding = BlueviaClient_Api_Constants::JSON,
            $query = null,
            $header_mods = array())
    {
        // test if allows removing of access_token headers & sign again
        $htt = new Zend_Http_Client();
        
        // take mms body and remove from json input if present        
        if (is_array($body) && !empty($body['mms_body'])) {
            $mms_body = $body['mms_body'];
            unset($body['mms_body']);
        }

        if ((is_array($body)) && ($encoding == BlueviaClient_Api_Constants::JSON)) {
            $msg_body = json_encode($body);
        } else {
        	if($encoding == BlueviaClient_Api_Constants::URL_ENCODED)
            	$msg_body = $this->getUrlEncoded($body);
            else			            	            
        		$msg_body = $body;
        }
                		
        $this->watchdog('info', 'Request: ' . $msg_body);
        $this->watchdog('info', 'URL: ' . $url);

        if (stripos($url, 'http') === FALSE)  { // if Url does not contain full path
            $url = $this->_rewriteUrl($url);
            $url = rtrim($this['baseUrl'], '/') . $url;
        }

        $client = $this->getHttpClient();
        $client->setConfig(array(
    				'maxredirects' => 0,
    				'timeout'      => 90));

        $client->setUri($url);
        
        $client->setMethod($method);
        $client->setRequestMethod($method);


        if ($encoding === 'application/json') {
            $client->setParameterGet('alt', 'json');
        }
        
        if ($query !== BlueviaClient::NO_V1PARAM) {
            $client->setParameterGet('version', 'v1');
        }
    
        // file attachments
        if (!empty($files)) {
            $client->setFileUpload('message', 'root-fields', $msg_body, 'application/json');
            // mms body inclusion as attachment
            if(!empty($mms_body))
            	$client->setFileUpload('textBody.txt', 'body', $mms_body, 'text/plain');            

            foreach ($files as $idx=>$file) {
            	if ($file != null) {
                	$name_id = sprintf("_%s%s", $idx, basename($file['path']));                
                	$client->setFileUpload($file['path'],$name_id,null,$file['mimetype']);
            	}
            }
        } else {            
            $client->setRawData($msg_body, $encoding);            
        }
                
        if (!empty($query) && is_array($query)) {
            foreach ($query as $queryParam => $value) {
                $client->setParameterGet($queryParam, $value);
            }
        }
        
    	if (!empty($body) && is_array($body) && ($encoding == BlueviaClient_Api_Constants::URL_ENCODED)) {
            foreach ($body as $bodyParam => $value) {
                $client->setParameterPost($bodyParam, $value);
            }
        }
        
        if(!empty($header_mods) && is_array($header_mods)) {
        	foreach ($header_mods as $headermod => $value) {
                $client->setParameterPost($headermod, $value);
            }
        }
        
        try {        	
            $this->_lastResponse = $client->request();
            $this->_lastRequest = $client->getLastRequest();
            $body = json_decode($this->_lastResponse->getBody());
        } catch(Zend_Http_Client_Exception $e) {
            throw new BlueviaClient_Exception_Client("Exception in request: <br/>".$e->getMessage());
        }

        
        // if no success on json_decode,
        if (empty($body))  {
            $body = $this->_lastResponse->getBody();
        }
        
        if (is_object($body) && $this->_lastResponse->isError()) {            
            if (!empty($body->ClientException) && is_object($body->ClientException)) {
            	$client->resetParameters(true);
                throw new BlueviaClient_Exception_Client($body->ClientException->text, $body->ClientException->exceptionId);
            } else if (!empty($body->ServerException) && is_object($body->ServerException)) {
            	$client->resetParameters(true);
                throw new BlueviaClient_Exception_Server($body->ServerException->text, $body->ServerException->exceptionId);
            }
        }
        // log response if logging is enabled
        $this->watchdog('info', 'Raw response: ' . print_r($body,1));
        $client->resetParameters(true);
        return $body;
    }

    public function getUrlEncoded($params) {
        // Get first query param        
        $queryparams = '';
        foreach($params as $key=>$value) {
            $queryparams .= '&'.rawurlencode($key) . '=' . rawurlencode($value);
        }
        $queryparams = /*'?' .*/ substr($queryparams, 1);

        return $queryparams;
    }

    

    // Implements ArrayAccess interface
    
    public function offsetExists($offset)
    {
        return isset($this->_options[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_options[$offset]) ? $this->_options[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->_options[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_options[$offset]);
    }

    public function isTwoLegged() {
        return empty($this->_context['user']['token_access'])
                && empty($this->_context['user']['token_secret']);
    }
        
    /**
     * Magic method to quickly generate getApi* calls
     * @return <type>
     */
    public function  __call($name, $arguments) {
        if (strstr($name, 'getApi')) { // if contains getApi
            $class = strtr($name, array('getApi' => ''));
            return $this->getService($class);
        } else {
            return null;
        }
    }
}
