<?php
/**
 * HttpAuthRequest class file
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * HttpAuthRequest provides methods for accessing
 * the Authorization HTTP request header.
 */
class HttpAuthRequest
{
	/** 
	 * Assigned to {@link errorCode} by {@link fetch()} 
	 * if Authentication header is missing 
	 */
	const ERROR_AUTH_HEADER_MISSING = 101;

	/** 
	 * Assigned to {@link errorCode} by {@link fetch()} 
	 * if Authentication header cannot be parsed 
	 */
	const ERROR_INVALID_AUTH_HEADER = 102;

	/**
	 * Holds error codes
	 * @var int
	 */
	public $errorCode = 0;

	/**
	 * The Http authorization scheme
	 */
	public $scheme = '';

	/**
	 * The Http authorization params
	 */
	public $params = '';

	/**
	 * Populates scheme and params
	 */
	public function __construct()
	{
		$this->fetch();
	}

	/**
	 * Poulates scheme and params property with 
	 * the auth-scheme, and the auth-params. These values are
	 * extracted from the 'Authorization' request header.
	 *
	 * @return array 	An associative array with two keys: auth_scheme and auth_params.
	 */
	public function fetch()
	{
		$headers = getallheaders();
		if( isset( $headers['Authorization'] ) )
		{
			$auth_header = explode( ' ', $headers['Authorization'] );
			if( count($auth_header) === 2 )
			{
				$this->scheme = strtolower($auth_header[0]);
				$this->params = $auth_header[1];
			}
			else
				$this->errorCode = self::ERROR_INVALID_AUTH_HEADER;
		}
		else
			$this->errorCode = self::ERROR_AUTH_HEADER_MISSING;
	}

}

?>
