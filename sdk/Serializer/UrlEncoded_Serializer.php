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
class URLEncoded_Serializer extends Generic_Serializer{
	
	/**
     * Method to serialize an array into an string
     * @param array $entity Entity to parse.
     * @return String containing the serialized content
     */
	public function serialize ($entity){
		parent::serialize($entity);
		return Utils::build_URLEncoded_query($entity);
	}
	

}