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

include_once 'Zend/Mime/Message.php';

/**
 * Enumerated class representing the mimetypes supported by Bluevia   
 */
final class BlueviaClient_Messaging_Mimetype {
	const TEXT_PLAIN = 'text/plain';
    const IMAGE_JPEG = 'image/jpeg';
    const IMAGE_BMP = 'image/bmp';
    const IMAGE_GIF = 'image/gif';
    const IMAGE_PNG = 'image/png';
    const AUDIO_AMR = 'audio/amr';
    const AUDIO_MIDI = 'audio/midi';
    const AUDIO_MP3 = 'audio/mp3';
    const AUDIO_MPEG = 'audio/mpeg';
    const AUDIO_WAV = 'audio/wav';
    const VIDEO_MP4 = 'video/mp4';
    const VIDEO_3GPP = 'video/3gpp';

    // Ensures that this class acts like an enum
    // and that it cannot be instantiated
    private function __construct(){}
    public static function isValid($value) {
        //retrieve the class constants
        $priority=new BlueviaClient_Messaging_Mimetype();
        $reflection = new ReflectionObject($priority);
        $valid_values = $reflection->getConstants();
        //check if the value is correct
        return in_array ($value, $valid_values);
    }
}

/**
 * Class representing a MIME content.
 */
final class BlueviaClient_MimeContent {	
	/** @var string $_contentType */
	public $_contentType;
	/** @var string $_content*/
	public $_content;
	/** @var string $_contentEncoding */
	public $_contentEncoding;
	/** @var string $fileName */
	public $_fileName;
}


/**
 * Class representing a received MMS. 
 * It contains the json body and the list of attachments 
 */
final class BlueviaClient_Received_MMS {
	
	/** @var stdClass $_message*/
	public $_message;
	/** @var BlueviaClient_MimeContent[] $_attachments*/
	public $_attachments = array();	
	
	public function __construct() {
		
	}
}
        
/**
 * Messaging Base Class 
 */
class BlueviaClient_Api_Messaging extends BlueviaClient_Api_Client_Base {

    // internal variables
    /** @var string $_from */
    protected $_from;
    /** @var  array  $_to */
    protected $_to = array();
    /** @var string $_message */
    protected $_message;
    /** @var array $_files */
    protected $_files = array();
    
    /**
     * Override of Constructor
     * @param BlueviaClient $unica
     */
    public function __construct(BlueviaClient $unica)
    {
        $this->setFrom($unica->getAccessToken());
        parent::__construct($unica);
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
            $url .= "/attachments" . ($attachment_id!='all'? '/'.$attachment_id:'');
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
     * Get All Received Messages to a specific shortcode     
     * @param string $registration_id ShortCode     
     * BlueviaClient_Exception
     */
    public function getMessages($registration_id) {
    	return self::_getReceivedMessage($registration_id);
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
     * Get the message delivery status
     *
     * @param string $ident Message identification
     * @return array of status objects
     * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response,
     * BlueviaClient_Exception
     */
    public function getDeliveryStatus($ident)
    {
        // check for required parameters
        $this->_checkParameter($ident,
            "Message Id cannot be null");
        $apiName = $this->_apiName;

        // format URL with parameters
        $url = '/REST/'.$apiName .'%ENV%/outbound/requests/' . $ident .
            '/deliverystatus';

        // do server request
        $response = $this->_unica->doRequest('GET', $url, null);

        // check response for error Codes
        $this->_checkResponse($response);
        // return response
        return $response;
    }

    // Common SETTERS & GETTERS

    /**
     * Remove  messaging parameters
     */
    public function reset()
    {
        $this->_from = null;
        $this->_to = array();
        $this->_message = null;
        return $this;
    }

    protected function setFrom($from) 
    {    	        
        $this->_from = array('alias' => $from);
        return $this;
    }

    /**
     * Defines a unique recipient for message
     * @param string $to  Destination (Hash or Phonenumber)
     * @param string $type (phoneNumber, alias, ...)
     * 
     */
    public function setRecipient($to, $type = 'phoneNumber')
    {
        $this->_to = array();
        if (!empty($to)) {
            $this->addRecipient($to, $type);
        }
        return $this;
    }

    /**
     * Adds a new recipient for message
     * @param string $to  Destination (Hash or Phonenumber)
     * @param string $type (phoneNumber, alias, ...)
     */
    public function addRecipient($to, $type = 'phoneNumber')
    {
        $this->_to[] = array($type => $to);
        return $this;
    }

    /**
     * Defines the message body
     * @param string $msg  Message Body
     */
    public function setMessage($msg)
    {
        $this->_message = $msg;
        return $this;
    }
   
    /**
     * Adds files to be attached in message (only for MMS)
     * @param string $filepath Full FilePath.
     * @param BlueviaClient_Messaging_Mimetype $mimetype Mimetype of the attached file. Must be one of the mimetype supported by Bluevia (text/plain, 
     * image/jpeg, image/bmp, image/gif, image/png, audio/amr, audio/midi, audio/mp3, audio/mpeg, audio/wav, video/mp4, video/avi, video/3gpp)
     *      
     */
    public function addFile($filepath, $mimetype)
    {
    	if (empty($filepath) || $filepath === "")
        	throw new BlueviaClient_Exception_Parameters( "The parameter filepath cannot be null or empty");
    	if (empty($mimetype) || $mimetype === "")
        	throw new BlueviaClient_Exception_Parameters( "The parameter mimetype cannot be null or empty");
        if(!BlueviaClient_Messaging_Mimetype::isValid($mimetype))
        	throw new BlueviaClient_Exception_Parameters( "The parameter mimetype is not valid");
        $this->_files[] = array('path' => $filepath, 'mimetype' => $mimetype);
        return $this;
    }

    /**
     * Internal use function to store available params into a structure
     * @param array  &$structure pass-by-reference position of structure
     * @param string $reference reference value
     * @param string $correlator correlator value
     */
     protected function _setCommonValues( &$structure,
                                            $endpoint = null,
                                            $correlator = null,
                                            $criteria = null) {
        // if endpoint is present
        if (!empty($endpoint)) {
            $structure['reference']['endpoint'] =  $endpoint;
        }

        // if Message correlator is present
        if (!empty($correlator)) {
            $structure['reference']['correlator'] = $correlator;
        }

        // if filtering criteria is present...
        if (!empty($criteria)) {
            $structure['criteria'] = $criteria;
        }
    }

    /**
    * ABSTRACT Helper to determine current class' API Name
    * @return string API Name
    * 
    * @throws BlueviaClient_Exception_Parameters
    */
    protected function _getAPIName() {
        throw new BlueviaClient_Exception_Parameters(
                        "Wrong call to abstract method getAPIName");
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
