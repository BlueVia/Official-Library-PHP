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
 * Include full API Package files
 */

include_once 'HTTP/Request2.php';
require_once 'Mail/mimeDecode.php';
/*****UTIL******/
include_once 'Extension/Utils.php';
include_once 'Extension/Enumerated.php';
include_once 'Extension/Constants.php';
/*******CONNECTOR********/
include_once "Connector/IAuth.php";
include_once "Connector/IConnector.php";
include_once "Connector/HTTP_Connector.php";
include_once "Connector/IOAuth.php";
include_once "Connector/OAuth_HTTP_Connector.php";
include_once "Connector/Generic_Response.php";
/*********EXCEPTION*******/
include_once "Exception/Exception.php";
include_once "Exception/Connector_Exception.php";
/*****SERIALIZER*****/
include_once 'Serializer/ISerializer.php';
include_once 'Serializer/Generic_Serializer.php';
include_once "Serializer/Json_Serializer.php";
include_once 'Serializer/UrlEncoded_Serializer.php';
include_once 'Serializer/Multipart_Serializer.php';
include_once 'Serializer/RPC_Serializer.php';
/****PARSER*****/
include_once 'Parser/IParser.php';
include_once 'Parser/Generic_Parser.php';
include_once 'Parser/URLEncoded_Parser.php';
include_once 'Parser/Json_Parser.php';
include_once 'Parser/XML_Parser.php';
include_once 'Parser/Multipart_Parser.php';
include_once 'Parser/RPC_Parser.php';
/*****CLIENTS*****/
include_once 'Clients/BV_Base_Client.php';
include_once 'Clients/BV_Advertising_Client.php';
include_once 'Clients/BV_Location_Client.php';
include_once 'Clients/BV_Directory_Client.php';
include_once 'Clients/BV_Mo_Client.php';
include_once 'Clients/BV_Mt_Client.php';
include_once 'Clients/BV_MoSms_Client.php';
include_once 'Clients/BV_MtSms_Client.php';
include_once 'Clients/BV_MtMms_Client.php';
include_once 'Clients/BV_MoMms_Client.php';
include_once 'Clients/BV_OAuth_Client.php';
/****API*****/
include_once 'Api/BV_Advertising.php';
include_once 'Api/BV_Location.php';
include_once 'Api/BV_Directory.php';
include_once 'Api/BV_MoSms.php';
include_once 'Api/BV_MtSms.php';
include_once 'Api/BV_MoMms.php';
include_once 'Api/BV_MtMms.php';
include_once 'Api/BV_OAuth.php';
include_once 'Api/BV_Payment.php';
/****SCHEMAS*****/
include_once 'Schemas/Messagery.php';
include_once 'Schemas/Advertising.php';
include_once 'Schemas/Directory.php';
include_once 'Schemas/Location.php';
include_once 'Schemas/Oauth.php';
include_once 'Schemas/Payment.php';

