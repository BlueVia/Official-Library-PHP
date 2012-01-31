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
 * Directory API
 */
class BlueviaClient_Api_Directory  extends BlueviaClient_Api_Client_Base {

    // valid Directory methods:
    /*@var USER_PROFILE valid method for getting user profile */
    const USER_PROFILE = 'UserProfile';
    /*@var USER_PROFILE valid method for getting user access information */
    const USER_ACCESS_INFO = 'UserAccessInfo';
    /*@var USER_PROFILE valid method for getting user terminal information */
    const USER_TERMINAL_INFO = 'UserTerminalInfo';



   // array of valid methods (@see USER_XXXX constants)
   protected $_valid_methods = null;

   /**
    * Constructs Directory API
    *
    * @param BlueviaClient $unica The UNICA API base instance
    * 
   */
   public function __construct($unica) {
       $this->_valid_methods = array(
                                self::USER_PROFILE,
                                self::USER_ACCESS_INFO,
                                self::USER_TERMINAL_INFO
        );
       parent::__construct($unica);
   }

   /**
    * Helper to determine current class' API Name
    * @return string API Name
    */
    protected function _getAPIName() {
        return 'Directory';
    }

    
    /**
     * This method is in charge of retrieving user information
     * @param  enum  $type Information to be retrieved. Can be one or more VALID_METHOD.
     * If null, the whole user information is retrieved
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     * @return User Info Object
     */
    public function getUserInfo($type = self::USER_PROFILE) {
       
        $guid = $this->_unica->getAccessToken();
        $guid_type = 'alias';

        // check required parameters
        if($type !== null) {
            $checkedtype = $type;
        } else {
            $checkedtype = '<empty>';
        }

        $this->_checkParameter(
                array($guid, $guid_type, $checkedtype),
                "Please, add parameters UserId, UserIdType and type"
        );


        // initialize values
        $_type = "";
        $_data_sets = "";

        // create URL depending on parameters
        if(!empty($type)) {
            if(is_array($type)) {                
                // verify types are one of the valid ones
                $this->_verifyTypes($type);
                // if no exceptions
                $_type = "/UserIdentities";
                $_data_sets = implode(',', $type);
            } else if(is_string($type)) {                
                // verify types are one of the valid ones
                $this->_verifyTypes($type);
                $_type = "/" . $type;                
            } else {
                throw new BlueviaClient_Exception_Parameters("Invalid type");
            }
        }
            
        // add optional parameters
        $params = array();
        if (!empty($_data_sets)) {
            $params['dataSets'] = $_data_sets;
        }

        // obtain real api name
        $apiName = $this->_apiName;

        // do SDK Request
        $response = $this->_unica->doRequest(
                'GET',
                "/REST/".$apiName ."%ENV%/". $guid_type . ":" . $guid .
                    "/UserInfo" . $_type,
                $params);

        // check response for errorCodes
        $this->_checkResponse($response);
        
        return $response;
        
    }


    /**
     * Helper function to verify type parameter
     * @param array|string $type
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     */
    protected function _verifyTypes($type) {
        if(!is_array($type)) {
            $type = array($type);
        }
        // check if it is on the types allowed
        foreach($type as $currentType) {
            if (!in_array($currentType, $this->_valid_methods)) {
                throw new BlueviaClient_Exception_Parameters("Wrong type sent");
            }
         }        
    }

    
}

