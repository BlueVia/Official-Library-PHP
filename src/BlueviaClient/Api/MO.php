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
 * Messaging class for MO
 */
class BlueviaClient_Api_MO extends BlueviaClient_Api_Messaging
{
/**
     * 
     * Helper function overriding Zend_Mime_Message::createFromMessage, due to a bug with the content-length key
     * @param Zend_Mime_Message $message
     * @param string $boundary
     * @param Zend_Mime $EOL
     */    
    private function _createFromMessage($message, $boundary, $EOL = Zend_Mime::LINEEND)
    {
    	require_once 'Zend/Mime/Decode.php';
        $parts = Zend_Mime_Decode::splitMessageStruct($message, $boundary, $EOL);

        $res = new Zend_Mime_Message;
        foreach ($parts as $part) {
            // now we build a new MimePart for the current Message Part:
            $newPart = new Zend_Mime_Part($part['body']);
            foreach ($part['header'] as $key => $value) {
                /**
                 * @todo check for characterset and filename
                 */
                switch(strtolower($key)) {
                    case 'content-type':
                        $newPart->type = $value;
                        break;
                    case 'content-transfer-encoding':
                        $newPart->encoding = $value;
                        break;
                    case 'content-id':
                        $newPart->id = trim($value,'<>');
                        break;
                    case 'content-disposition':
                        $newPart->disposition = $value;
                        break;
                    case 'content-description':
                        $newPart->description = $value;
                        break;
                    case 'content-location':
                        $newPart->location = $value;
                        break;
                    case 'content-language':
                        $newPart->language = $value;
                        break;
                    case 'content-length':
                    	break;
                    default:                        
                        break;
                }
            }            
            $res->addPart($newPart);
        }
        return $res;    	
    }
    /**
     * 
     * Helper function to parse a Zend_Mime_Part object
     * @param Zend_Mime_Part[] $mime_parts
     * @param BlueviaCliet_Received_MMS $received_mms
     * */
    private function _parseMimeParts($mime_parts,$received_mms) {
    	foreach($mime_parts as $part) {        	 			 
    		if(!(strpos($part->type,"application/json") === false)) {
    			// message part    			
    			$content = $part->getContent();    
    			if(!is_null($content)) {		
    				$message = json_decode($content);
    				if(!is_null($message))
    					$received_mms->_message = $message;
    			}
    		} else {
    			// attachment part
    			if(!(strpos($part->type,"multipart/") === false)){
    				$boundary = explode("boundary=",$part->type);
					$boundary[1] = str_replace("\"", '', $boundary[1]);
					$boundary[1] = str_replace("-", '', $boundary[1]);
					if(!(strpos($boundary[1],"--") === false)) $boundary[1] = substr($boundary[1],2);											
    				$attachments = $this->_createFromMessage($part->getContent(),$boundary[1]);
    				if(!is_null($attachments)) {
    					$mime_parts = $attachments->getParts();
    					$this->_parseMimeParts($mime_parts,$received_mms);	
    				}     								 		    							
    			} else {
    				$mime_content = new BlueviaClient_MimeContent();
	    			$mime_content->_contentType = $part->type;
	    			$mime_content->_contentEncoding = $part->encoding;
	    			$mime_content->_fileName = $part->filename;
	    			$mime_content->_content = $part->getContent();
	    			$received_mms->_attachments[] = $mime_content;	
    			}    								
    		}
        }
    }
	/**
     * Stop Message notification
     * @param string $subscription_id
     *
     * @return Response object
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
     * 
     */
    protected function _stopMessageNotification ($subscription_id) {
        // check required parameters
        $this->_checkParameter($subscription_id,
                "Subscription id must be provided");

        // obtain real api name
        $apiName = $this->_apiName;
        // do server request
        $response = $this->_unica->doRequest(
                    'DELETE',
                    "/REST/".$apiName ."%ENV%/inbound/subscriptions/".
                        rawurlencode($subscription_id), ''
                );

        // check response for error codes
        $this->_checkResponse($response);

        return $response;

    }
    

    
    /**
     * Get a Message obtained previously in getReceivedMessages
     * @see getReceivedMessages     
     * @param string $registration_id ShortCode
     * @param string $message_id Unique message Id
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception      
     */
	public function getMessage($registration_id, $message_id) {
    	return self::_getReceivedMessage($registration_id, $message_id);
    }

    /**
     * Get Messages + Get Received Message 
     * @param int $registration_id MO Registration Identifier
     * @param int $message_id message unique identifier
     * @param int|string $attachment_id attachment identifier or 'all'
     * (corresponding to the specific message)
     * @return BlueviaClient_Received_MMS The received message
     * 
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     */
    protected function _getReceivedMessage (
            $registration_id,
            $message_id = null,
            $attachment_id = null) {

        $apiName = $this->_apiName;

        $url = "/REST/".$apiName ."%ENV%/inbound/$registration_id/messages";

        // if message id is present, then ask for specific message Id
        if (!empty($message_id) && is_string($message_id)) {
            $url .= "/" . $message_id;
        }
        // if attachment id is present, then ask for specific attachment id
        if (!empty($attachment_id) && is_string($attachment_id)) {
            //$url .= "/attachments" . ($attachment_id!='all'? '/'.$attachment_id:'');
            $url .= "/attachments" .$attachment_id;
        }
        
        $response = $this->_unica->doRequest(
                    'GET',
                    $url,
                    null
        );

        $this->_checkResponse($response);
		
        if((!is_null($message_id)) && (!is_null($response)))
        {        	
        	$content_type_header = $this->_unica->getLastResponse()->getHeader("Content-type");
        	$exploded_content_type = explode(';', $content_type_header);
        	if($exploded_content_type[0] === "multipart/form-data"){
        		$boundary_array = explode('=',$exploded_content_type[1]);
        		$start = explode('=',$exploded_content_type[2]);
        		if($boundary_array[0] === "boundary") { 
        			$boundary = $boundary_array[1];
        		} else {
		        	$boundary_start = (strpos($response, "--") + 2);
		        	$boundary_end = strpos($response,"\n",$boundary_start);
		        	$length = ($boundary_end - $boundary_start);
		        	$boundary = substr($response,$boundary_start,$length);
		        	$boundary = str_replace("\r", '', $boundary);
        	 	}        	 	
        	 	$mime_message = Zend_Mime_Message::createFromMessage($response, $boundary);
        	 	if(!is_null($mime_message)){
        	 		$received_mms = new BlueviaClient_Received_MMS();        	 		
        	 		$mime_parts = $mime_message->getParts();
        	 		$this->_parseMimeParts($mime_parts,$received_mms);
        	 		if(!is_null($received_mms))
        				return $received_mms;        	 		        	 		        	 	
        	 	}
        	} else {
        		throw new BlueviaClient_Exception_Response("Unexpected response. No multipart/form-data found");	
        	}
        }        
        return $response;
    }
	
    /**
     * Abstract Helper to return notification type
     * @param string $endpoint The URL Endpoint (should be https)
     * @param string $correlator Correlator
     * @param string $filterCriteria Mo Keyword
     *
     * @return array
     * @throws BlueviaClient_Exception_Parameters
     */
    protected function _getDeliveryReceiptNotificationType(
            $endpoint,
            $correlator,
            $filterCriteria = null) {
        
        throw new BlueviaClient_Exception_Parameters(
                        "Wrong call to abstract method
                            get_delivery_receipt_notification_type");
    }
    
}