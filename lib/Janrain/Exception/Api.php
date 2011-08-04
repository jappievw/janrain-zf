<?php
/**
 * Exception which is thrown when the Janrain API responded
 * with an error.
 * 
 * Error codes as of today:
 *  -1 => Service Temporarily Unavailable
 *   0 => Missing parameter
 *   1 => Invalid parameter
 *   2 => Data not found
 *   3 => Authentication error
 *   4 => Facebook Error
 *   5 => Mapping exists
 *   6 => Error interacting with a previously operational provider
 *   7 => Engage account upgrade needed to access this API
 *   8 => Missing third-party credentials for this identifier
 *   9 => Third-party credentials have been revoked
 *  10 => Your application is not properly configured
 *  11 => The provider or identifier does not support this feature
 *  12 => Google Error
 *  13 => Twitter Error
 *  14 => LinkedIn Error
 *  15 => LiveId Error
 *  16 => MySpace Error
 *  17 => Yahoo Error
 *  18 => Domain already exists
 *  19 => App ID not found
 *  20 => Orkut Error
 * 
 * @link http://documentation.janrain.com/api-request-response-format
 */
class Janrain_Exception_Api extends Janrain_Exception
{

}