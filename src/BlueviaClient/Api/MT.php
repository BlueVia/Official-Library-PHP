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
abstract class BlueviaClient_Api_MT extends BlueviaClient_Api_Messaging
{
	
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
    
    public abstract function send();
    
}