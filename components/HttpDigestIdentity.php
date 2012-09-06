<?php
/**
 * HttpDigestIdentity class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * HttpDigestIdentity allows authentication through HTTP Digest authentication.
 *
 * @author Z. Alem <info@alemcode.com>
 */
class HttpDigestIdentity extends HttpIdentity
{
	public $www_authenticate = array(
		'auth_scheme' => 'Digest',
		'realm' => 'Restricted Area',
		'qop' => '',
		'nonce' => '',
		'opaque' => '',
	);

	public $username = null;

	public $password = null;

	public $use_php_http_auth = true;

	/**
	 * @see HttpIdentity::makeAuthenticateHeader()
	 */
	public function makeAuthenticateHeader()
	{
		$nonce = uniqid();
		$www_authenticate_string = 'WWW-Authenticate: Digest realm="'.$this->www_authenticate['realm'];
		$www_authenticate_string.= '",qop="auth",nonce="'. $nonce .'",opaque="'.md5($this->www_authenticate['realm']).'"';

		return array( $www_authenticate_string );
	}


	/** HTTP headers to extract, and the class properties to populate */
	public function processAuthExtract( )
	{
		if( $this->use_php_http_auth === true )
			return $this->phpHttpAuth();
		else
		{
		}

	}

	/**
	 * Uses PHP pre-populated $_SERVER values to get base64 decoded 
	 * credentials
	 */
	public function phpHttpAuth()
	{
		if( isset( $_SERVER['PHP_AUTH_DIGEST'] ) )
		{
			return true;
		}
		$this->errorCode = self::ERROR_FAILED_EXTRACT_PROCESSING;
		return false;
	}

	public function http_digest_parse($digest)
	{
		//protect against missing data
		$required_params = array('nonce'=>true, 'nc'=>true, 'cnonce'=>true, 'qop'=>true, 'username'=>true, 'uri'=>true, 'response'=>true);
		$data = array();
		$keys = implode('|', array_keys($required_params));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			if( $match[3] )
				$data[$match[1]] = $match[3];
			else
				$data[$match[1]] = $match[4];
			unset($required_params[$match[1]]);
		}

		if( $required_params !== array() )
			return false;
		else 
			return $data;
	}
}
?>
