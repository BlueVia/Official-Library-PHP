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

include_once "XML/Serializer.php";

class BlueviaClient_Api_Client_RPC extends BlueviaClient_Api_Client_Base {
    
    /**
     * Helps generating XML for JSON_XML_RPC_REQUEST
     * @param <type> $url The URI of the endpoint
     * @param <type> $method
     * @param <type> $params
     * @return <type>
     */
    protected function _RPCRequest($url, $method, $params = null, $header_mods = null) {
        $call = array(
            'methodCall'=>array(
                'id'        => time(),
                'version'   => 'v1',
                'method'    => $method,
                'params'    => $params,
            )
        );
        //xmlns:sms="http://www.telefonica.com/schemas/UNICA/REST/sms/v1/" xmlns:dir="http://www.telefonica.com/schemas/UNICA/REST/directory/v1/" xmlns:mms="http://www.telefonica.com/schemas/UNICA/REST/mms/v1/" xmlns:com="http://www.telefonica.com/schemas/UNICA/REST/common/v1" xmlns="http://www.telefonica.com/schemas/UNICA/RPC/payment/v1"
        $xml_request = sprintf('<?xml version="1.0"  encoding="UTF-8" standalone="yes"?>
             <ns2:methodCall  xmlns="http://www.telefonica.com/schemas/UNICA/RPC/definition/v1" xmlns:ns2="http://www.telefonica.com/schemas/UNICA/RPC/payment/v1" xmlns:ns3="http://www.telefonica.com/schemas/UNICA/RPC/common/v1">
              <id>%s</id> 
              <version>%s</version>              
              <ns2:method>%s</ns2:method>
              %s
            </ns2:methodCall>',
         $call['methodCall']['id'],
         $call['methodCall']['version'],
         $method,$this->_serializeToXml($params));

        $response = $this->_unica->doRequest(
                'POST',
                $url,
                $xml_request,
                array(),
                BlueviaClient_Api_Constants::XML,
                BlueviaClient::NO_V1PARAM,
                $header_mods
        );
        $this->_checkResponse($response);


        return $response;
    }

    /**
     * Serializes the request to XML RPC
     * @param <type> $obj
     * @return <type>
     */
    protected function _serializeToXml($obj) {
        if (empty($obj)) return null;

        $serialized = '';
        $serializer = new XML_Serializer();

        $options = array();
        $options['rootName'] = 'ns2:params';
        $options['linebreak'] = '';

        if ($serializer->serialize($obj, $options)) {
            $serialized = $serializer->getSerializedData();

            $serialized = str_replace(
                    array('</XML_Serializer_Tag>', '<XML_Serializer_Tag>'),
                    '',
                    $serialized
            );
            return $serialized;
        }
        else {
            return null;
        }
    }

      /**
     * Simplifies Payment response XML
     * @param <XMLReader> $xml
     * @return array of creative elements
     */
    protected function _simplifyResponse(/*string*/$xml_response, $expected_elements) {
        // generate xml reader
        $xml = new XMLReader();
        $xml->XML($xml_response);
        $assoc_response = array();

        while ($xml->read()) {
            $tagname = strtolower($xml->name);
            if ($xml->nodeType === XMLReader::ELEMENT) {

                if (in_array($tagname, $expected_elements)) {
                    $xml->read();
                    if ($xml->hasValue) {
                        $assoc_response[$tagname] = $xml->value;
                    }
                }
            }
        }
        $xml->close();

        return $assoc_response;
    }
}