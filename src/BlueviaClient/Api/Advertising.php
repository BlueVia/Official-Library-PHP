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
 * Advertising API
 */
class BlueviaClient_Api_Advertising extends BlueviaClient_Api_Client_Base {

	/**
	 * Helper to determine current class' API Name
	 * @return <string> API Name
	 */
	protected function _getAPIName() {
		return 'Advertising';
	}

	/**
	 * Helper to generate a random Ad request ID
	 * @return <string> Ad request ID
	 */
	private function _getComposedAdRequestId($ad_space) {
		if(strlen($ad_space) > 10)
		$ad_request_id = substr($ad_space, 0, 10);
		else
		$ad_request_id = $ad_space;
		$ad_request_id .= time();
		if(!$this->_unica->isTwoLegged())
		$ad_request_id .= $this->_unica->getAccessToken();
		return $ad_request_id;
	}

	/**
	 * Fetch an AD
	 * @param array $params array(
	 *                      user_agent (R),
	 *                      ad_request_id (R),
	 *                      ad_space(R),
	 *                      max_ads (O),
	 *                      protection_policy (R),
	 *                      ad_presentation (O)
	 *                      ad_presentation_size (O)
	 *                      keyword (O)
	 *                      country (O) ISO-3166 Country Code
	 *                      target_user_id (O)
	 * )
	 * @param bool $return_asoc If true, parses xml with XMLReader class
	 * @throws BlueviaClient_Exception_Parameters, BlueviaClient_Exception_Response
	 * @return Ad Object
	 */
	public function request($params, $return_asoc = true) {
		if (empty($params['ad_space']) || $params['ad_space'] === "")
			throw new BlueviaClient_Exception_Parameters( "The parameter ad_space cannot be null");

		// if ad_request_id param is empty it is composed by the library
		if (empty($params['ad_request_id']) || $params['ad_request_id'] === "")
			$params['ad_request_id'] = $this->_getComposedAdRequestId($params['ad_space']);
			
		// check other required parameters
		$this->_checkParameter(
		array(
		$params['user_agent'],
		$params['ad_request_id'],		
		$params['protection_policy'],
		),
                "The Parameters user_agent, ad_request_id, ad_space,
                    protection policy cannot be null"
                    );

                    // country is only required in two-legged
                    if ($this->_unica->isTwoLegged()) {
                    	$this->_checkParameter(
                    	$params['country'],
                    "An ISO-3166 country is required in two legged authorization"
                    );

                    }

                    // extra 'null' checkings
                    foreach ($params as $key => $value) {
                    	switch ($key) {
                    		case 'ad_presentation':
                    			$this->_checkValue($key, $value);
                    			break;
                    		case 'ad_presentation_size':
                    			$this->_checkValue($key, $value);
                    			break;
                    		case 'keyword':
                    			$this->_checkValue($key, $value);
                    			break;
                    	}
                    }


                    // FORM URL ENCODE Query Params
                    //$queryparams = $this->_unica->getUrlEncoded($params);

                    // obtain API Name
                    $apiName = $this->_apiName;

                    // do Request
                    $response = $this->_unica->doRequest(
                        'POST',
                        "/REST/" . $apiName . "%ENV%/simple/requests",
                    	$params,
                    	null,
                        BlueviaClient_Api_Constants::URL_ENCODED,
                        null 
                        );

                        // check response Error Codes
                        $this->_checkResponse($response);

                        // return XML Response
                        if (!empty($response) && $return_asoc) {
                        	$xmlr = new XMLReader();
                        	$xmlr->XML($response);

                        	//$response = $this->_xml2assoc($xmlr, "root");
                        	$response = $this->_simplify_ad($xmlr);
                        	$xmlr->close();
                        }

                        return $response;
	}



	/**
	 * Simplifies Advertising XML
	 * @param XMLReader $xml
	 * @return array of creative elements
	 */
	protected function _simplify_ad(/*@var XMLReader */ $xml) {
		/* @var array */
		$creative_elements = array();
		/* @var array */
		$current_element = array();
		/* @var string */
		$ad_type = null;
		/* @var string type of presentation (e.g 0101) */
		$ad_presentation = null;
		$type_id = '';

		while ($xml->read()) {
			$name = strtolower($xml->name);
			if (stripos($name, 'resource') !== FALSE) {
				// ensure it is start element
				if ($xml->nodeType === XMLReader::ELEMENT) {
					$ad_presentation = $xml->getAttribute('ad_presentation');
					if(!empty($ad_presentation)) {
						$type_id = $ad_presentation;
					}
				}
			} else if (stripos($name, 'creative_element') !== FALSE) {
				// reset row: new element reinitialize
				if ($xml->nodeType === XMLReader::END_ELEMENT) {
					if (!empty($current_element)) {
						$creative_elements[]= $current_element;
					}
					$current_element = array();
					$ad_type = null;
					$ad_presentation = null;
				} else {
					$ad_type = $xml->getAttribute('type');
					if(!empty($ad_type)) {
						$current_element['type_name'] = $ad_type;
						// always the same
						$current_element['type_id'] = $type_id;
					}
				}
			} else if (stripos($name, 'attribute') !== FALSE) {
				// ensure it is start element
				if ($xml->nodeType === XMLReader::ELEMENT) {
					$xml2 = new XMLReader();
					if ($xml->hasAttributes) {
						$type = trim($xml->getAttribute('type'));
						switch($type) {
							case 'adtext':
							case 'locator':
								$xml->read();
								if ($xml->hasValue) {
									$current_element['value'] = $xml->value;
								}

								break;
							case 'URL':
								$xml->read();
								if ($xml->hasValue) {
									$current_element['interaction'] = $xml->value;
								}

								break;

						}
					}
				}
			};



		}
		return $creative_elements;
	}

}
