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
 * Messaging class for SMS
 */
class BlueviaClient_Api_SMS extends BlueviaClient_Api_Messaging
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
     * Sends SMS Message
     *
     * @param string $endpoint Your Server's url receiving notifications
     * @param string $correlator The correlator Identifier
     *
     * @return string   The send request identifier
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     */
    public function send($endpoint = null, $correlator = null)
    {
    	if ($this->_from === NULL || $this->_from['alias'] === NULL) {
    		$this->setFrom($this->_unica->getAccessToken());
    	}
 	
        // check required parameters
        $this->_checkParameter(
                array($this->_from, $this->_to, $this->_message),
                "Please, set 'from', 'message' and 'to'"
                );

       
        // construct JSON body
        $body = array(
            'smsText' => array(                
                'originAddress' => array(
                    $this->_from
                ),
                'address' => $this->_to,
                'message' => $this->_message,
            )
        );

       
        if (!empty($endpoint)) {
            $body['smsText']['receiptRequest']['endpoint'] =  $endpoint;
        }
        
        if (!empty($correlator)) {
            $body['smsText']['receiptRequest']['correlator'] = $correlator;
        }
        
        $apiName = $this->_apiName;
        // do server Request
        $response = $this->_unica->doRequest(
                'POST',
                '/REST/'.$apiName .'%ENV%/outbound/requests',
                $body
                );

        // check response for error codes
        $this->_checkResponse($response);
        
        // obtain message id from header location
        $location = $this->_unica->getLastResponse()->getHeader('Location');
        $ident = preg_replace(
                '@^.+/requests/([^/]+)/deliverystatus$@',
                '$1',
                $location
                );

        return $ident;
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



    /** 
     * Helper to return sms Delivery Receipt Notification type
     * 
     * @param string $endpoint
     * @param int $correlator
     * @param string $filterCriteria
     * 
     * @return string 
     */
    protected function _getDeliveryReceiptNotificationType(
            $endpoint,
            $correlator,
            $filterCriteria = null) {
        
        // form body
        $notifType = array(
             'deliveryReceiptNotification' => array(
                    'reference'=>array(
                    
                    ),
                    'originAddress'=> $this->_from,
              )
         );

         // add optional values
         $this->_setCommonValues($notifType['deliveryReceiptNotification'],
                                    $endpoint,
                                    $correlator,
                                    $filterCriteria
                 );
        
         
         return $notifType;
    }
}
