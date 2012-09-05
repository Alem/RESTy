<?php
/**
 * HttpIdentity class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * HttpIdentity allows authentication of credentials passed 
 * as HTTP headers. 
 *
 * The default implementation is a custom Http Authentication
 * named 'Unencoded' which sends the username and password in unencoded text.
 *
 * This is, of course, not recommended as if the username or password contains
 * a reserved/special-use character in HTTP (e.g. a colon ), the HTTP request 
 * may become malformed.
 *
 * @author Z. Alem <info@alemcode.com>
 */
class HttpIdentity extends CBaseUserIdentity
{

	const ERROR_AUTH_HEADER_MISSING = 101;

	const ERROR_INVALID_AUTH_HEADER = 102;

	const ERROR_FAILED_EXTRACT_PROCESSING = 103;

	public $extracted_auth_header = null;

	public $www_authenticate = array(
		'auth_scheme' => 'Unencoded',
		'realm' => 'Restricted Area'
	);

	public $username = null;

	public $password = null;

	public $test_mode = false;

	public $test_credentials = array( 'username' => '', 'password'=>'' );

	/**
	 * An array of the HTTP headers that will be presented upon access to a protected resource
	 * This will accompany the HTTP 401 status code
	 *
	 * @return array 	HTTP WWW-Authenticate headers
	 */
	public function makeAuthenticateHeader()
	{
		$www_authenticate_string = 'WWW-Authenticate: ' . $this->www_authenticate['auth_scheme']; 
		$www_authenticate_string.= ' realm="'.$this->www_authenticate['realm'].'"';
		return array( $www_authenticate_string );
	}

	/**
	 * Poulates extracted_auth_header property with an array containing two values:
	 * the auth-scheme, and the auth-params. These values are
	 * extracted from the 'Authorization' request header.
	 *
	 * @return array 	An associative array with two keys: auth_scheme and auth_params.
	 */
	public function extractAuthHeader()
	{
		$headers = getallheaders();
		if( isset( $headers['Authorization'] ) )
		{
			$auth_header = explode( ' ', $headers['Authorization'] );

			if( count($headers) === 2 )
			{
				return array(
					'auth_scheme' => strtolower($auth_header[0]),
					'auth_params' => $auth_header[1],
				);
			}
			else
				$this->errorCode = ERROR_INVALID_AUTH_HEADER;

		}
		else
			$this->errorCode = ERROR_AUTH_HEADER_MISSING;

		return null;
	}


	/**
	 * HTTP authentication forms must implement
	 * any processing of extracted header data
	 * in this method.
	 *
	 * Default implementation parses a colon delimited 
	 * username:password string and populates the username
	 * and password properties
	 *
	 * @param string $auth_params 	Auth params from the Authorization request header.
	 * @return bool True on success
	 */
	public function processAuthExtract( $auth_params )
	{
		$credentials = explode( ':', $auth_params );	

		if( count( $credentials ) === 2 )
		{
			$this->username = $credentials[0];
			$this->password = $credentials[1];
			return true;
		}
		else
		       	return false;
	}

	/**
	 * Verify ApiUser exists for username and 
	 * password properties
	 *
	 * @return bool True on success
	 */
	public function verifyIdentity()
	{
		$api_user = ApiUser::model()->findByAttributes(
			array('username'=>$this->username)
		);

		if( 
			$api_user === null 
			|| ( $api_user->password !== md5($this->password) )
		)
			$this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
		else
			$this->errorCode = self::ERROR_NONE;

		return ($this->errorCode === self::ERROR_NONE);
	}

	/**
	 * Authenticates a user.
	 *
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$auth_header = $this->extractAuthHeader();

		if( $auth_header === null )
			return false;

		if( $this->processAuthExtract( $auth_header ) !== true )
			return false;

		if( $this->test_mode )
		{
			return(
				$this->username === $this->test_credentials['username'] 
				&& $this->password === $this->test_credentials['password'] 
			);
		}
		else
			return $this->verifyIdentity();
	}

}
?>
