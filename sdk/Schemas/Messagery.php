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
 * Class containing the attachment path in your file system and content-type
 * @author Telefonica R&D
 *
 */
class Attachment{

	/**
	 *  String $attachment path to the attachment file
	 */
	public $attachment;
	/**
	 *
	 * String $type the attachemnt mime-type
	 */
	public $type;

	/**
	 * Constructor
	 * @param String $attachment path to the attachment file
	 * @param String $type the attachemnt mime-type
	 */
	public function __construct($attachment,$type){
		Utils::checkParameter(array('$attachment'=>$attachment,'$type'=>$type));
		Utils::checkPath($attachment);
		$type=BV_Mimetype::getValue($type);
		$this->attachment=$attachment;
		$this->type=$type;
	}
}

/**
 * Abstract class representing the abstract SMS/MMS class that represents the different messages that
 * will be recieved using the SMS/MMS Client API
 * @author Telefonica R&D
 *
 */
class Abstract_Message{

	/**
	 *  array of String $destination phone numbers phone number to which this message is sent.
	 */
	public $destination;
	/**
	 *  String $originAddress the sender number. (Token or phone number)
	 */
	public $originAddress;
	/**
	 *  $dateTime the date and time when the message was sent.
	 */
	public $dateTime;
}

/**
 * Class representing a Mime Content (an attachment) of a received MMS.
 *
 * @author Telefonica R&D
 *
 */
final class Mime_Content {
	/**
	 *  string $contentType the content type of the attachemnt
	 */
	public $contentType;
	/**
	 *  string $content the content of the attachments. The object will be a String for text attachemtns or InputStream for binary attachents.
	 */
	public $content;
	/**
	 *  string $contentEncoding the content encoding of the attachment
	 */
	public $encoding;
	/**
	 *  string $name name of the attachment, if exists
	 */
	public $name;
}

/**
 *
 * Class representing the  elements that will be received using the BV_MoMms_Client::getAllMessages method
 *
 *
 * @author Telefonica R&D
 *
 */
final class Mms_Message_Info extends Abstract_Message{
	/**
	 *  String $messageId the message identifier.
	 */
	public $messageId;
	/**
	 *  String $subject the mms subject
	 */
	public $subject;
	/**
	 *  array of AttachmentInfo $attachmentInfo An array containing all the attachment information.
	 **/
	public $attachmentInfo;
}

/**
 * Class representing  the attachment information retrieved when you use the getAllMessages function and the $attachmentUrl parameter is set to true.
 * @author Telefonica R&D
 *
 */
final class Attachment_Info{
	/**
	 *  String $url The attachment ID. This parameter is used in the getAttachment function to recieve an attachment.
	 */
	public $url;
	/**
	 *  String $contentType. The attachment content-type.
	 */
	public $contentType;
}
/**
 * Class representing the  elements that will be received using the BV_MoMms_Client::getMessage method
 * It contains the message info (Mms_Message_Info) and the list of attachments (MimeContent)
 * @author Telefonica R&D
 */
final class Mms_Message {

	/**
	 *  Mms_Message_Info $mmsInfo the message info
	 */
	public $mmsInfo;
	/**
	 *  array of AttachmentInfo $attachments.
	 */
	public $attachments = array();

}
/**
 * Class representing the Sms message elements that will be received using the SMS Client API
 * @author Telefonica R&D
 */
class Sms_Message extends Abstract_Message{
	/**
	 * String $message the basic info of the message.
	 */
	public $message;
}
/**
 * Class representing the delivery information you will receive when you use the getDeliveryStatus function in the Sms/Mms API.
 * @author Telefonica R&D
 *
 */
class Delivery_Info{

	/**
	 * String $destination phone number to which the message was sent.
	 */
	public $destination;
	/**
	 * String $status the delivery status information.
	 */
	public $status;
	/**
	 * String $statusDescription the status description.
	 */
	public $statusDescription;
}


/**
 *
 * Class representing the delivery status of a previous sent message either for SMS or MMS
 *
 * @author Telefonica R&D
 *
 */


class Status extends Enumerated{

	/**
	 * DELIVERED_TO_NETWORK The message was delivered to network
	 */
	const DELIVERED_TO_NETWORK = 'DeliveredToNetwork';
	/**
	 * DELIVERY_UNCERTAIN It is not possible to ascertain whether the message was delivered
	 */
	const DELIVERY_UNCERTAIN = 'DeliveryUncertain';
	/**
	 * 
	 * DELIVERY_IMPOSSIBLE Unsuccessful delivery
	 */
	const DELIVERY_IMPOSSIBLE = 'DeliveryImpossible';
	/**
	 * 
	 * MESSAGE_WAITING The message is still queued for delivery.
	 */
	const MESSAGE_WAITING = 'MessageWaiting';
	/**
	 * 
	 * DELIVERED_TO_TERMINAL The message has been successful delivered to the handset.
	 */
	const DELIVERED_TO_TERMINAL = 'DeliveredToTerminal';
	/**
	 * 
	 * DELIVERY_NOTIFICATION_NOT_SUPPORTED Unable to provide delivery status information because it is not supported
										by the network.
	 */
	const DELIVERY_NOTIFICATION_NOT_SUPPORTED = 'DeliveryNotificationNotSupported';

	public static $descriptions = array(
	self::DELIVERED_TO_NETWORK => 'The message has been delivered to the network.
									Another state could be available later to inform if the message 
									was finally delivered to the handset.',
	self::DELIVERY_UNCERTAIN => 'Delivery status unknown.',
	self::DELIVERY_IMPOSSIBLE => 'Unsuccessful delivery; the message could not be delivered before it expired.',
	self::MESSAGE_WAITING => 'The message is still queued for delivery. This is a temporary state, pending transition
									 to another state.',
	self::DELIVERED_TO_TERMINAL => 'The message has been successful delivered to the handset.',
	self::DELIVERY_NOTIFICATION_NOT_SUPPORTED => 'Unable to provide delivery status information because it is not supported
										by the network.'
										);

}

/**
 * Class representing the mimetypes supported by Bluevia
 */
final class BV_Mimetype extends Enumerated{
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
	const VIDEO_AVI = "video/avi";


}
