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
 * Messaging class for SMSMO
 */
class BlueviaClient_Api_Smsmo extends BlueviaClient_Api_MO
{
	    /**
    * Helper to determine current class' API Name
     * 
    * @return string API Name
    */
    protected function _getAPIName() {
        return 'SMS';
    }
	
 /**
     * Send a startSMSNotification request to the endpoint
     * 
     * @param string $endpoint (basically an owned HTTP server)
     *          to deliver SMS notification     
     * @param string $correlator unique identifier (optional)
     * @param string $criteria
     * 
     * @return the location where the notification can be checked
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     */
    public function startSmsNotification(
            $endpoint,
            $correlator = null,
            $criteria = null
            ) {

        // check required paramters
        $this->_checkParameter(
                array($this->_from, $this->_to, $endpoint, $correlator),
            "Origin and destination number, endpoint and correlator cannot be null.");
        
        // set message body
        $params = array(
            'smsNotification' => array(               
                'destinationAddress' => $this->_to                               
             )
        );

        // assign optional values (endpoint, correlator & criteria)
        $this->_setCommonValues( $params['smsNotification'],
                            $endpoint,
                            $correlator,
                            $criteria
                );
        
        $apiName = $this->_apiName;
        // do server Request
        $response = $this->_unica->doRequest(
                'POST',
                '/REST/'.$apiName .'%ENV%/inbound/subscriptions',
                $params
                );
        
        // check response for error codes
        $this->_checkResponse($response);
        
        // obtain Notification Id from location header
        $location = $this->_unica->getLastResponse()->getHeader('Location');
        $ident = preg_replace('@^.+/subscriptions/([^/]+)@', '$1', $location);

        return $ident;
    }
    
 /**
     * Get All Received Messages to a specific shortcode     
     * @param string $registration_id ShortCode     
     * BlueviaClient_Exception
     */
    public function getMessages($registration_id) {
    	return self::_getReceivedMessage($registration_id);
    }
    
    /**
     * stopSMSNotification
     *
     * @param string $subscription_id
     *
     * @return 200 OK if success
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     */
    public function stopSmsNotification($subscription_id) {
        return parent::_stopMessageNotification($subscription_id);
    }

    /**
     * Get (pooling method) received message by ID
     * @param int $registration_id
     * @return <object> Message
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     */
    public function getReceivedSms($registration_id) {
        return parent::_getReceivedMessage($registration_id);
    }
}