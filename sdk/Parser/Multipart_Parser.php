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
 * Class to parse multipart HTTP body responses.
 * @author Telefonica R&D
 *
 */
class Multipart_Parser extends Json_Parser{

	/**
	 * @param String $byteArray The string to be parsed.
	 * @return stdClass standard PHP class with the parsed information of the $byteArray param 
	 */
	public function parse($byteArray){
		parent::parse($byteArray);
		$structure=$this->_multipartDecoder($byteArray);
		$boundary='-'.$structure->headers[''];
		$array=explode($boundary,$structure->body);
		$rootFields=$array[0];
		$structure=$this->_multipartDecoder($array[1]);
		$attachments=$this->_parseAttachments($structure->parts);
		$message = new stdClass();
		$message->attachments=$attachments;
		$message->message=parent::parse($rootFields);
		if (is_null($message))
		return ($byteArray);
		else
		return $message;
	}

	/**
	 * Helper class to decode a multipart
	 * @param String $multipart The multipart returned by the server
	 *
	 * @return StdClass object with the multipart information
	 */
	private function _multipartDecoder($multipart){
		$multipart=str_replace('form-data','mixed',$multipart);
		$params = array ('include_bodies' => true,
						'decode_bodies' => false,
						'decode_headers' => false);
		$decoder = new Mail_mimeDecode($multipart);
		return $decoder->decode($params);
	}

	/**
	 * Helper class to create the array with the MimeContent
	 * @param $parts Multipart parts
	 *
	 * @return array of BlueviaClient_MimeContent()
	 *
	 */
	private function _parseAttachments($parts){

		$attachments=array();
		foreach ($parts as $key =>$value){
			$attachments[$key]=new Mime_Content();
			$attachments[$key]->content=$value->body;
			$attachments[$key]->contentType=$value->headers['content-type'];
			if (isset($value->headers['content-location'])){
				$attachments[$key]->name=$value->headers['content-location'];
			}
			if (!empty($value->headers['content-transfer-encoding']))
			$attachments[$key]->encoding=$value->headers['content-transfer-encoding'];
		}
		return $attachments;
	}
}