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
 * Generic Base Class
 */
class BlueviaClient_Api_Client_Base
{
    /** @var BlueviaClient */
    protected $_unica;
    /** @var string */
    protected $_apiName;

    /** @var array **/
    protected $_lasterror = null;

    /**
     *
     * @param BlueviaClient $unica The UNICA API base instance
     */
    public function __construct(BlueviaClient $unica)
    {
        $this->_unica = $unica;
        $this->_apiName = $this->_getAPIName();
    }


    /**
     * Obtains the last error (if exists),
     * if no error is present returns null response.
     * 
     * @return array|string Last error information
     */
     public function getLastError() {
         return $this->_lasterror;
     }



     /**
     * Helper to Determine if last error code was an error (~20X)
     * @param $response The response object
     * 
     * @return The response code
     * @throws BlueviaClient_Exception_Response if response contains errorCode
     */
    protected function _checkResponse($response) {
        // get Response status code
        $response = $this->_unica->getLastResponse();

        if (empty($response)) {
            return null;
        }

        $code = $this->_unica->getLastResponse()->getStatus();

        // if code != 2XX it is an error
        if ($code < 200 || $code > 299) {
            if($response instanceof Zend_Http_Response) {
                $body = $response->getBody();
                if(!empty($body)) {
                    $output = $response->getBody();
                }
            } else if (is_object($response)) {
                $output = $response->body;
            } else if (is_string($response)) {
                $output = $response;
            } else {
                $output = $body;
            }

            $this->_lasterror = $this->_processError($output);
            if (!empty($this->_lasterror['text'])) {
                $output = "[".$this->_lasterror['exceptionId'] . "] " .
                        $this->_lasterror['text'];
            }
            throw new BlueviaClient_Exception_Response($output, $code);
        } else {
            $this->_lasterror = null;
        }

        return $code;
     }

     
    /**
     * Helper to determine if required parameter is empty
     * @param <string|array> $parameter_value. Parameter, or array of parameters.
     * @param <string> $error_string. Error displayed
     * @throws BlueviaClient_Exception_Parameters if missing parameters
     */
    protected function _checkParameter($parameter_value, $error_string) {
        if (!is_array($parameter_value)) {
            $parameter_value = array($parameter_value);
        }

        foreach($parameter_value as $key => $value) {
            if (empty($value) || $value === "") {
                throw new BlueviaClient_Exception_Parameters(
                        $error_string . " (failed on ".$key . ")");
            } 
        }
    }

    /**
     * Helps checking if a value is empty
     * @param <type> $key
     * @param <type> $value
     */
    protected function _checkValue($key, $value) {
        if ($value === null || $value === '') {
            throw new BlueviaClient_Exception_Parameters(
                    'a valid request is expected for ' . $key);
        }
    }


    /**
     * Helper to make SDK users reading of XML errors easier
     * @param string $xml
     * @return array
     */
    protected function _processError($_xml) {
        if (is_string($_xml)) {
            $xml = new XMLReader();
            $xml->XML($_xml);
        } else {
            return $_xml;
        }

        $error = array();

        try {
            while ($xml->read()) {
                // ensure it is start element
                if ($xml->nodeType === XMLReader::ELEMENT) { 
                    $name = $xml->name;
                    $xml->read();
                    if($xml->hasValue) {
                        $value = trim($xml->value);
                        if (empty($value)) {
                            $value = $name;
                            $name = "type";
                        }
                        $error[$name] = $value;
                    }
                }
            }
        } catch(Exception $e) {
            return $_xml;
        }
        return $error;
    }
        

    /**
     * Abstract method to obtain Current API Name, used on common inheritance
     * @return <string> API Name
     */
    protected function _getAPIName() {
        throw new BlueviaClient_Exception_Parameters(
                        "Wrong call to abstract method getAPIName");
    }
    
    /**
     * Method to modify the clients context setting a new OAuth token
     * @param string $token
     * @param string $token_secret
     */
    protected function _setToken($token,$token_secret) {
    	if((!empty($token)) && (!empty($token_secret))) {
    		$old_context = $this->_unica->getContext();
    		$application_context = array(
			    'app' => array(
			      'consumer_key' => $old_context['app']['consumer_key'],
			      'consumer_secret' => $old_context['app']['consumer_secret']        
			    ),
				'user' => array(
			      'token_access' => $token,
			      'token_secret' => $token_secret
			    )
			);
			unset($this->_unica);
    		$this->_unica = new BlueviaClient($application_context);
    	} else {
    		throw new BlueviaClient_Exception_Parameters("Token cannot be empty");
    	}    	    	
    }
}
