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
 * Class representing the status of a previous payment.
 * @author Telefonica R&D
 *
 */
class Payment_Status_Result{
	/**
	 * String: element indicating the status of the payment. Possible values: SUCCESS/FAILURE/PENDING.
	 */
	public $transactionStatus;
	/**
	 * String: element containing further information related to the transactionStatus.
	 */
	public $transactionStatusDescription;
}

/**
 * Class representing the response of a previous payment.
 * @author Telefonica R&D
 *
 */
class Payment_Result extends Payment_Status_Result{
	/**
	 * String: element containing an identifier of the payment just done, uniquely identifying it.
	 */
	public $transactionId;
}
