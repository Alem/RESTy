<?php
/**
 * HttpBasicIdentity class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * HttpBasicIdentity allows authentication through HTTP Basic
 * authentication.
 *
 * Note: This class depends on the availability of PHP_AUTH_* $_SERVER variables, which is not
 * present if PHP is running through CGI. If this is the case, use_php_http_auth should be set to
 * false. However, the alternative option will only work for the Apache web server.
 *
 * @author Z. Alem <info@alemcode.com>
 */
class HttpBasicIdentity extends HttpIdentity
{
	/**
	 * @see HttpIdentity::www_auth
	 */
	public $www_auth = array(
		'auth_scheme' => 'Basic',
		'realm' => 'Restricted Area'
	);

	/**
	 * @see HttpIdentity::username
	 */
	public $username = '';

	/**
	 * @see HttpIdentity::password
	 */
	public $password = '';

	/**
	 * Set to false if running PHP as a CGI script on an apache webserver.
	 *
	 * PHP's builtin support for http auth will not work if running as a CGI script. 
	 * The alternative only works for the apache webserver.
	 */
	public $use_php_http_auth = false;

	/** HTTP headers to extract, and the class properties to populate */
	public function processAuthExtract()
	{
		if( $this->use_php_http_auth === true )
			return $this->phpHttpAuth();
		else
		{
			$this->HttpAuthRequest->params = base64_decode( $this->HttpAuthRequest->params );
			return parent::processAuthExtract();
		}

	}

	/**
	 * Uses PHP pre-populated $_SERVER values to get base64 decoded 
	 * credentials
	 */
	public function phpHttpAuth()
	{
		if( isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) )
		{
			$this->username = $_SERVER['PHP_AUTH_USER'];
			$this->password = $_SERVER['PHP_AUTH_PW'];
			return true;
		}
		$this->errorCode = self::ERROR_FAILED_EXTRACT_PROCESSING;
		return false;
	}

}
?>
