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
	const TEXT_PLAIN = 'text/plain;charset=UTF-8';
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
