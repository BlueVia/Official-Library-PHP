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
class Json_Serializer extends Generic_Serializer{

	/**
	 * Method to serialize an array into an string
	 * @param array $entity Entity to parse.
	 * @return String containing the serialized content
	 */
	public function serialize ($entity){
		try{
			parent::serialize($entity);
			$this->_detectEncoding($entity);
			$json_encoded= json_encode($entity);
			$replacedString = preg_replace("/\\\\u([0-9abcdef]{4})/", "&#x$1;",$json_encoded );
			$json=mb_convert_encoding($replacedString, 'UTF-8', 'HTML-ENTITIES');
			return $json;
		} catch (Exception $e){
			throw $e;
		}
	}

	// PRIVATE FUNCTIONS
	private function _detectEncoding (&$entity){
		foreach ($entity as $key => $value){
			if (is_array($value)){
				$this->_detectEncoding($value);
				$entity[$key]=$value;
			}else{
				$entity[$key]= Utils::convertToUTF8($value);
			}
		}
	}

}

