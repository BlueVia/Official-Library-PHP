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
 * Class that represents the serializer object. Object implementing this interface
 * will be able to serialize an entity object into an HTTP body request
 *
 */
class Multipart_Serializer  extends  Json_Serializer{

	/**
	 * Method to serialize an array into an string
	 * @param array $entity Entity to parse.
	 * @return String containing the serialized content
	 */
	public function serialize ($entity){
		$rootFields=parent::serialize($entity['root_fields']);
		$serialized_Object['root_fields']=array($this->_bodyToFile($rootFields),
												'message',
												'application/json;charset=UTF-8');
		if (!empty($entity['message'])){
			$message=Utils::convertToUTF8($entity['message']);
			$serialized_Object['body']=array($this->_bodyToFile($message),
										'textBody.txt',
										'text/plain;charset=UTF-8');
		}
		if (!empty($entity['files'])){
			foreach ($entity['files'] as $idx=>$file) {
				if ($file != null) {
					if ($file['mimetype']==BV_Mimetype::TEXT_PLAIN){
						try{
							$fp=$this->_convertFileEncoding($file['path']);
						}catch (Exception $e){
							throw new Bluevia_Exception('-116',null,$file['path']);
						}
					}
					else{
						$fp=$file['path'];
					}
						$name_id = sprintf("_%s%s", $idx, basename($file['path']));
						$serialized_Object[$name_id]=array($fp,
						basename($file['path']),
						$file['mimetype']);
					
				}
			}
		}
		return $serialized_Object;
	}

	// PRIVATE FUNCTIONS

	/**
	 * Creates a temp file and writes $body content into it
	 * @param String $body The text to be written in the file
	 * @return A file pointer
	 */
	private function _bodyToFile($body){
		$fp = fopen("php://temp", '+r');
		fputs($fp, $body);
		return $fp;
	}

	/**
	 * Helper function to convert the text file's content to UTF-8
	 * @param string $file File path of the content to convert.
	 * @return FilePointer to the new file encoded in UTF-8.
	 */
	private function _convertFileEncoding($file){
		$content=file_get_contents($file);
		if (!$content){
			throw new Bluevia_Exception('-116');
		}
		$content=Utils::convertToUTF8($content);
		return $this->_bodyToFile($content);
	}

}
