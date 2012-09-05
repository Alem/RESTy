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
	public $www_authenticate = array(
		'auth_scheme' => 'Basic',
		'realm' => 'Restricted Area'
	);

	public $username = null;

	public $password = null;

	public $use_php_http_auth = true;

	/**
	 * Skipped if using PHP's native http authorization support
	 */
	public function extractAuthHeader()
	{
		if( $this->use_php_http_auth === true )
			return true;
		else
			return parent::extractAuthHeader();
	}

	/** HTTP headers to extract, and the class properties to populate */
	public function processAuthExtract()
	{
		if( $this->use_php_http_auth === true )
			return $this->phpHttpAuth();
		else
		{
			$decoded_params = base64_decode( $this->extracted_auth_header['auth_params'] );
			return parent::processAuthExtract( $decoded_params );
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
