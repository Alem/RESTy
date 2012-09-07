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
	public $www_auth= array(
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
		$www_authenticate_string = 'WWW-Authenticate: Digest realm="'.$this->www_auth['realm'];
		$www_authenticate_string.= '",qop="auth",nonce="'. $nonce .'",opaque="'.md5($this->www_auth['realm']).'"';

		return array( $www_authenticate_string );
	}


	/** HTTP headers to extract, and the class properties to populate */
	public function processAuthExtract()
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
			$this->parseHttpDigest( $_SERVER['PHP_AUTH_DIGEST'] );
		}
		$this->errorCode = self::ERROR_FAILED_EXTRACT_PROCESSING;
		return false;
	}

	public function validateResponse( $data, $username, $password )
	{
		$a1 = $data['username'].':'.$this->www_auth['realm'].':'.$password;
		$a2 = $_SERVER['REQUEST_METHOD'].':'.$data['uri'];
		
		$ha1 = md5( $a1 );
		$ha2 = md5( $a2 );

		$response_raw = $a1.':'.$data['nonce'].':'.$data['nc'];
		$response_raw.= ':'$data['cnonce'].':'.$data['qop'].':'.$a2;

		$valid_response = md5($response_raw);
	}

	public function parseHttpDigest($digest)
	{
		//protect against missing data
		$required_params = array('nonce'=>true, 'nc'=>true, 'cnonce'=>true, 'qop'=>true, 'username'=>true, 'uri'=>true, 'response'=>true);
		$data = array();
		$keys = implode('|', array_keys($required_params));

		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

		foreach($matches as $match) 
		{
			if( $match[3] )
				$data[$match[1]] = $match[3];
			else
				$data[$match[1]] = $match[4];
			unset( $required_params[$match[1]] );
		}

		if( $required_params !== array() )
			return false;
		else 
			return $data;
	}
}
?>
