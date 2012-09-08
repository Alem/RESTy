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
abstract class HttpIdentity extends CBaseUserIdentity
{

	const ERROR_FAILED_EXTRACT_PROCESSING = 103;

	public $HttpAuthRequest = null;

	public $www_auth = array();

	public $username = null;

	public $password = null;

	public $test_mode = false;

	public $test_credentials = array( 'username' => 'api_user', 'password'=>'api_key' );

	public function __construct( $HttpAuthRequest )
	{
		$this->HttpAuthRequest = $HttpAuthRequest;
	}

	/**
	 * An array of the HTTP headers that will be presented upon access to a protected resource
	 * This will accompany the HTTP 401 status code
	 *
	 * @return array 	HTTP WWW-Authenticate headers
	 */
	public function makeAuthenticateHeader()
	{
		$www_authenticate_string = 'WWW-Authenticate: ' . $this->www_auth['auth_scheme']; 
		$www_authenticate_string.= ' realm="'.$this->www_auth['realm'].'"';
		return array( $www_authenticate_string );
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
	 * @return bool True on success
	 */
	public function processAuthExtract()
	{
		$credentials = explode( ':', $this->HttpAuthRequest->params );	

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
		if( $this->processAuthExtract() !== true )
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
