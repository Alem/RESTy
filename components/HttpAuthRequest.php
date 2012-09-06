<?php
/**
 * HttpAuthRequest provides methods for accessing
 * the Authentication HTTP request header.
 *
 */
class HttpAuthRequest
{
	const ERROR_AUTH_HEADER_MISSING = 101;

	const ERROR_INVALID_AUTH_HEADER = 102;

	/**
	 * Holds error codes
	 * @var int
	 */
	public $errorCode = 0;

	/**
	 * The Http authorization scheme
	 * @var string
	 */
	public $scheme = null;

	/**
	 * The Http authorization params
	 * @var string
	 */
	public $params = null;

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

			if( count($headers) === 2 )
			{
				$this->scheme = strtolower($auth_header[0]);
				$this->params = $auth_header[1];
			}
			else
				$this->errorCode = ERROR_INVALID_AUTH_HEADER;

		}
		else
			$this->errorCode = ERROR_AUTH_HEADER_MISSING;
	}

}

?>
