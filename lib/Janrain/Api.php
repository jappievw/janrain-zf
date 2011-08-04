<?php
/**
 * Class which can talk to the Janrain api. 
 * 
 * @author jvanwanrooy
 */
class Janrain_Api
{
	/**
	 * Holds the api key for communication with Janrain.
	 * 
	 * @var string
	 */
	protected $_api_key;
	
	/**
	 * Holds the Zend Http Client for use with communicating
	 * to the Janrain api.
	 * 
	 * @var Zend_Http_Client
	 */
	protected $_http_client;
	
	/**
	 * Holds the default configuration for the Http client.
	 * 
	 * @var array
	 */
	protected $_client_config = array(
		'adapter'       => 'Zend_Http_Client_Adapter_Curl',
		'timeout'       => 10,
		'maxredirects'  => 0,
		'useragent'     => 'php-janrain-api/1.0',
		'keepalive'     => true,
		'storeresponse' => false
	);
	
	/**
	 * Holds the base url for communicating with the Janrain api.
	 * When changing: keep in mind the final trailing slash is included!
	 * 
	 * @var string
	 */
	protected $_baseurl = 'https://rpxnow.com/api/v2/';
	
	/**
	 * Constructs a Janrain api object.
	 * 
	 * @param string $api_key
	 * @param array $client_config
	 */
	public function __construct($api_key, $client_config = array())
	{
		$this->_api_key        = $api_key;
		$this->_client_config += $client_config;
	}
	
	/**
	 * Get the configured Janrain api key.
	 * 
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->_api_key;
	}
	
	/**
	 * Performs the auth_info call.
	 * 
	 * @param string $token
	 * @param bool $extended
	 * @param string $token_url
	 * @return stdClass
	 */
	public function getAuthInfo($token, $extended = null, $token_url = null)
	{
		$params = array();
		$params['token'] = $token;
		if (!is_null($extended))
		{
			$params['extended'] = ($extended === true) ? 'true' : 'false';
		}
		if (!is_null($token_url))
		{
			$params['tokenUrl'] = $token_url;
		}
		
		return $this->_callApi('auth_info', $params);
	}
	
	/**
	 * Set the mapping information between a Janrain identifier and a local
	 * primary key.
	 * 
	 * @param string $identifier
	 * @param string $primary_key
	 * @param bool $overwrite
	 * @return stdClass
	 */
	public function setMap($identifier, $primary_key, $overwrite = null)
	{
		$params = array();
		$params['identifier'] = $identifier;
		$params['primaryKey'] = $primary_key;
		if (!is_null($overwrite))
		{
			$params['overwrite'] = ($overwrite === true) ? 'true' : 'false';
		}
		
		return $this->_callApi('map', $params);
	}
	
	/**
	 * Unmap a user from a specific local primary key.
	 * 
	 * @param string $identifier
	 * @param string $primary_key
	 * @param bool $unlink
	 * @return stdClass
	 */
	public function setUnmap($identifier, $primary_key, $unlink = null)
	{
		$params = array();
		$params['identifier'] = $identifier;
		$params['primaryKey'] = $primary_key;
		if (!is_null($unlink))
		{
			$params['unlink'] = ($unlink === true) ? 'true' : 'false';
		}
		
		return $this->_callApi('unmap', $params);
	}
	
	/**
	 * Unmap all identifiers which belong to a local primary key. 
	 * 
	 * @param string $primary_key
	 * @param bool $unlink
	 * @return stdClass
	 */
	public function setUnmapAll($primary_key, $unlink = null)
	{
		$params = array();
		$params['all_identifiers'] = 'true';
		$params['primaryKey'] = $primary_key;
		if (!is_null($unlink))
		{
			$params['unlink'] = ($unlink === true) ? 'true' : 'false';
		}
		
		return $this->_callApi('unmap', $params);
	}
	
	/**
	 * Get the mappings of a specific local primary key.
	 * 
	 * @param string $primary_key
	 * @return stdClass
	 */
	public function getMappings($primary_key)
	{
		$params = array();
		$params['primaryKey'] = $primary_key;
		
		return $this->_callApi('mappings', $params);
	}
	
	/**
	 * Get all mappings for the application.
	 * 
	 * @return stdClass
	 */
	public function getAllMappings()
	{
		return $this->_callApi('all_mappings');
	}
	
	/**
	 * Get all contacts of an openid identifier.
	 * 
	 * @param string $identifier
	 * @return stdClass
	 */
	public function getContacts($identifier)
	{
		$params = array();
		$params['identifier'] = $identifier;
		
		return $this->_callApi('get_contacts', $params);
	}
	
	/**
	 * Call the Janrain api with the parameters specified. 
	 * 
	 * @param string $path
	 * @param array $params
	 * @throws Janrain_Exception_Request When an error occurred during the request.
	 * @throws Janrain_Exception_Response When the response is not conform the Janrain documentation.
	 * @throws Janrain_Exception_Api When the api returned an error.
	 * @return stdClass
	 */
	protected function _callApi($path, $params = array())
	{
		/**
		 * Get an http client object and set the uri.
		 */
		$client = $this->_getHttpClient();
		$client->setUri($this->_getApiUrl($path));
		
		/**
		 * Set the params on the request and complete them
		 * with the api key and format.
		 */
		$params += array(
			'apiKey' => $this->getApiKey(),
			'format' => 'json'
		);
		$client->setParameterPost($params);
		
		/**
		 * Perform the HTTP request.
		 */
		try
		{
			$response = $client->request();
		}
		catch (Zend_Http_Client_Exception $e)
		{
			throw new Janrain_Exception_Request("Error occurred while processing the api request.", null, $e);
		}
		
		/**
		 * Convert the HTTP response into a json object.
		 */
		try
		{
			$response_obj = json_decode($response->getBody());
		}
		catch (Zend_Json_Exception $e)
		{
			throw new Janrain_Exception_Response("Error occurred during processing of the Janrain response.", null, $e);
		}
		
		/**
		 * When the API request was executed properly, but the API itself
		 * gave an unexpexted reply, throw an exception.
		 */
		if (!isset($response_obj->stat) || !in_array($response_obj->stat, array('ok', 'fail')))
		{
			$context = get_defined_vars();
			$ex = new Janrain_Exception_Response("There was no 'stat' property or it contained an invalid value.");
			$ex->setContext($context);
			throw $ex;
		}
		
		/**
		 * When the API request gave a response with a fail, then an
		 * exception needs to be thrown.
		 */
		if ($response_obj->stat === 'fail')
		{
			throw new Janrain_Exception_Api($response_obj->err->msg, $response_obj->err->code);
		}
		
		/**
		 * Everything went okay :).
		 */
		return $response_obj;
	}
	
	/**
	 * Get the url for the api with the specified path.
	 * 
	 * @param string $path
	 * @return string
	 */
	protected function _getApiUrl($path)
	{
		return $this->_baseurl . $path;
	}
	
	/**
	 * Gives a zend http client for an api request.
	 * The http client is reused between different requests.
	 * The params will be reset with every _getHttpClient call.
	 * 
	 * @return Zend_Http_Client
	 */
	protected function _getHttpClient()
	{
		/**
		 * Create an http object when it isn't initiated yet.
		 * When it is already initiated, reset the params and
		 * headers of the previous request.
		 */
		if (!($this->_http_client instanceof Zend_Http_Client))
		{
			$this->_http_client = new Zend_Http_Client($this->_getApiUrl(''), $this->_client_config);
			$this->_http_client->setMethod(Zend_Http_Client::POST);
		}
		else
		{
			$this->_http_client->resetParameters(true);
		}
		
		return $this->_http_client;
	}
}