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
class BlueviaClient_Api_Mmsmo extends BlueviaClient_Api_MO
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
     * Get 
     */
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
     * Get Received Message
     *
     * @param string $registration_id Shortcode number
     * @param boolean $useAttachmentURLs  bool flag to get message attachments information (attachment_id and content_type). It's an optional parameter (false value by default)
     *
     * @return ReceivedMessagesType
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception
     */
    public function getMessages($registration_id, $useAttachmentURLs = false) {
       $apiName = $this->_apiName;

        $url = "/REST/".$apiName ."%ENV%/inbound/$registration_id/messages";
        
    	$useAttachmentURLs=($useAttachmentURLs) ? 'true' : 'false';
        $params = array ('useAttachmentURLs'=>$useAttachmentURLs);
        $response = $this->_unica->doRequest(
                    'GET',
                    $url,
                    null,
                    null,
                    BlueviaClient_Api_Constants::JSON,
                    $params
        );
        $this->_checkresponse($response);
        
      	return $response; 
    }
  
    /**
     * Get the Attachment $attachment_id
     * 
     * @param string $registration_id Shortcode number
     * @param string $message_id Unique message Id
     * @param string $attachment_id Attachment Id
     * 
     * @return attachment 
     */
    public function getAttachment($registration_id, $message_id, $attachment_id){

        $apiName = $this->_apiName;

        $url = "/REST/".$apiName ."%ENV%/inbound/$registration_id/messages";

        // if message id is present, then ask for specific message Id
        if (!empty($message_id) && is_string($message_id)) {
            $url .= "/" . $message_id;
        }
        // if attachment id is present, then ask for specific attachment id
        if (!empty($attachment_id) && is_string($attachment_id)) {
            //$url .= "/attachments" . ($attachment_id!='all'? '/'.$attachment_id:'');
            $url .= "/attachments" ."/" .$attachment_id;
        }
        
        $response = $this->_unica->doRequest(
                    'GET',
                    $url,
                    null
        );

        $this->_checkResponse($response);
        return $response;
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