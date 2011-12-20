<?php
/**
 *
 * @category    opentel
 * @package     com.bluevia
 * @copyright   Copyright (c) 2010-2011 Telefónica I+D (http://www.tid.es)
 * @author      Bluevia <support@bluevia.com>
 * @version     1.0
 *
 * BlueVia is a global iniciative of Telefonica delivered by Movistar and O2.
 * Please, check out http://www.bluevia.com and if you need more information
 * contact us at support@bluevia.com
 */


/**
 * Base class for Location API
 */
class BlueviaClient_Api_Location extends BlueviaClient_Api_Client_Base {

    /**
    * Helper to determine current class' API Name
    * @return string API Name
    */
    protected function _getAPIName() {
        return 'Location';
    }

    /**     
     * @param BlueviaClient $bv     
     */
    public function __construct(BlueviaClient $bv){
        parent::__construct($bv);
    }
  
    /**
     * The Application invokes getLocation to Retrieve the ‘TerminalLocation’ or
     * TerminalLocationforGroup
     *          
     * @param string $accAccuracy Accuracy that is acceptable for a response   
     */
    public function getLocation($accAccuracy = null) {
               
        $locatedParty = new BlueviaClient_Schemas_UserIdType($this->_unica->getAccessToken(),BlueviaClient_Schemas_UserIdType::ALIAS);
        $locatedParty = array($locatedParty);
                
        // assign mandatory params
        $params = array('locatedParty' => $this->_transformToUrl($locatedParty));
        
        if(!is_null($accAccuracy))
        	if($accAccuracy === '')
        		throw new BlueviaClient_Exception_Parameters("Empty accAccuracy parameter is not valid");
        	else
        		$params['acceptableAccuracy'] = $accAccuracy;
                
        $apiName = $this->_apiName;

        // do Request
        $response = $this->_unica->doRequest(
                'GET',
                "/REST/" . $apiName . "%ENV%/TerminalLocation",
                null,
                null,
                'application/json',
                $params
                );
        
        $this->_checkResponse($response);

        return $response;
    }

    
    /**
     * Helper to transform to URL comma sepparated a multi-valued field     
     * */
    protected function _transformToUrl($field) {

        if (!is_array($field)) {
            $field = array($field);
        }
        $refactorized = array();
        foreach ($field as $value) {
            if(is_a($value, 'BlueviaClient_Schemas_UserIdType') ) {
            	if($value->__isset('alias'))
            		$refactorized[] = $value->toUrl('alias');
            	else if($value->__isset('phoneNumber'))
            		$refactorized[] = $value->toUrl();
           		else 
           			throw new BlueviaClient_Exception_Parameters(
                    "Not allowed UserIdType"
                );
            } else {
                throw new BlueviaClient_Exception_Parameters(
                    "Expected instance of BlueviaClient_Schemas_UserIdType"
                );
            }
        }

        return implode(',', $refactorized);
    }

    /**
     *
     * @param string $value
     * @param array $valid_values
     * @return boolean True if value is one of expected
     */
    protected function _verifyValues($value, $valid_values) {
        if(in_array($value, $valid_values)) {
            return true;
        } else {
            return false;
        }
    }



}
