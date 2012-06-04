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
 * Interface that will be implemented by REST clients that will allow to
 * manage REST requests and responses
 *
 * @author Telefonica R&D
 */
interface IConnector{

	/**
	 * Creates a request using REST to the gSDP server in order to create a Generic_Response
	 * using POST method
	 *
	 * @param String $address the uri to create the entity remotely via REST
	 * @param String $parameters The query parameters for the request
	 * @param String $content the body of the request
	 * @param String $headers headers of the request
	 * @return Generic_Response the response of the operation
	 * @throws Bluevia_Exception
	 */
	public function create ($address,$parameters,$content,$headers);

	/**
	 * Creates a request using REST to the gSDP server in order to retrieve a Generic_Response from the server
	 * using GET method
	 *
	 * @param String $address the uri to create the entity remotely via REST
	 * @param String $parameters the parameters in order to do the filtering
	 * @return Generic_Response the response of the operation
	 * @throws Bluevia_Exception
	 */
	public function retrieve ($address,$parameters);

	/**
	 * Creates a request using REST to the gSDP server in order to create a Generic_Response from the server
	 * using UPDATE method
	 * 
	 * @param String $address the uri to create the entity remotely via REST
	 * @param String $parameters The query parameters for the request
	 * @param String $content the body of the request
	 * @param String $headers headers of the request
	 * @return Generic_Response the response of the operation
	 * @throws Bluevia_Exception
	 */
	public function update ($address,$parameters,$content,$headers);

	/**
	 * Creates a request using REST to the gSDP server in order to retrieve a Generic_Response from the server
	 * using DELETE method
	 *
	 * @param String $address the uri to create the entity remotely via REST
	 * @param String $parameters the parameters in order to do the filtering
	 * @return Generic_Response the response of the operation
	 * @throws Bluevia_Exception
	 */
	public function delete ($address,$parameters);

}