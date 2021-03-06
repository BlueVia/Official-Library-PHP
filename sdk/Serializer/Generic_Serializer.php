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
 * Abstract class that represents the serializer object. Object implementing this interface
 * will be able to serialize an entity object into an HTTP body request
 *
 */
abstract class Generic_Serializer implements ISerializer{
	
	 /**
     * Method to serialize an array into an string
     * @param array $entity Entity to parse.
     * @return String containing the serialized content
     */
	 public function serialize($entity){
	 	Utils::checkParameter(array('$entity'=>$entity));
	 }
}
