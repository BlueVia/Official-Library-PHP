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
 * MMS MO Class
 */
class BlueviaClient_Api_MMS extends BlueviaClient_Api_Messaging
{
    /** @var string $_subject */
    protected $_subject = '';
    /** @var string $_sender */
    protected $_sender = '';
    

    /**
    * Helper to determine current class' API Name
    * @return string API Name
    */
    protected function _getAPIName() {
        return 'MMS';
    }


    /**
     * Clean MMS parameters
     */
    public function reset()
    {
        parent::reset();        
        $this->_subject = '';       
        $this->_files = array();
        
        return $this;
    }

    /**
     * set MMS message subject
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }
   

    /**
     * Sends MMS Message
     *
     * @param $endpoint Your Server Notification endpoint
     * @param $correlator Your correlator string
     *
     * @return string   The send request identifier
     * 
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception
     */
    public function send($endpoint = null, $correlator = null)
    {      
        // check if all required parameters are present        
    	if ($this->_from === NULL || $this->_from['alias'] === NULL) {
    		$this->setFrom($this->_unica->getAccessToken());
    	}
    	
        // fills JSON message body
        $message = array(
            'message' => array(                                
                'originAddress' => $this->_from,
                'address' => $this->_to,                
            ),
            'mms_body' => $this->_message,
        );
        
        // add optional paramters
        if (!empty($this->_subject)) {
            $message['message']['subject'] = $this->_subject;
        }

        if (!empty($endpoint)) {
            $message['message']['receiptRequest']['endpoint'] =  $endpoint;
        }
        
        if (!empty($correlator)) {
            $message['message']['receiptRequest']['correlator'] = $correlator;
        }

        $apiName = $this->_apiName;
        // do SDK Request to server
        $response = $this->_unica->doRequest(
                'POST',
                '/REST/'.$apiName .'%ENV%/outbound/requests',
                $message,
                $this->_files
                );
        
        // obtain response error codes (if present)
        $this->_checkResponse($response);

        // get Location Header
        $location = $this->_unica->getLastResponse()->getHeader('Location');
        
        // take message id from location header and return
        $ident = preg_replace('@^.+/requests/([^/]+)/deliverystatus$@', '$1', $location);
        
        return $ident;
    }
     

    /**
     * Get Received Message
     *
     * @param string $registration_id Shortcode number
     * @param string $message_id Unique message Id
     * @param string $attachments Attachment Id
     *
     * @return ReceivedMessagesType
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception
     */
    public function getReceivedMms($registration_id, $message_id = null, $attachments = null) {
        return parent::_getReceivedMessage($registration_id, $message_id, $attachments);
    }
  
     /**
     * Stop MMS Notification
     * @param string $subscription_id
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception
     * @return 200 OK if performed right
     */
    public function stopMmsNotification($subscription_id) {
        return parent::_StopMessageNotification($subscription_id);
    }

    /**
     * Start Message notification (MMS)
     * @param string $endpoint
     * @param string $criteria
     * @param string $correlator
     * @return notification id
     */
    public function startMmsNotification($endpoint, $correlator, $criteria = null) {
        /*$this->_checkParameter(array($this->_to, $endpoint),
                "Origin number and endpoint cannot be null");*/
        $params = array(
            'messageNotification' => array(                
                'destinationAddress' => $this->_to,                
             )
        );

        // assign optional values (endpoint, correlator & criteria)
        $this->_setCommonValues(
                $params['messageNotification'],
                $endpoint,
                $correlator,
                $criteria
        );
        
        
        $apiName = $this->_apiName;
        $response = $this->_unica->doRequest(
                'POST', '/REST/'.$apiName .'%ENV%/inbound/subscriptions',
                $params
        );

        $location = $this->_unica->getLastResponse()->getHeader('Location');
        $ident = preg_replace('@^.+/subscriptions/([^/]+)@', '$1', $location);


        $this->_checkResponse($response);

        return $ident;
    }    
}
