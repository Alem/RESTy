<?php
/**
 * HttpAuthFilter class file
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * Allows automated authentication of controller actions.
 */
class HttpAuthFilter extends CFilter
{
	/**
	 * Authentication schemes accepted
	 *
	 * Options: 
	 * - 'Basic' for Basic Authentication, 
	 * - 'Digest' for Digest Authentication
	 */
	public $accepted_auth_schemes = array();

	/**
	 * Performs HTTP Basic Authentication
	 * before execution of a RestController method
	 *
	 * @param CFilterChain $filter 	The filter chain
	 */
	public function preFilter( $filterChain )
	{
		$HttpAuthRequest = new HttpAuthRequest();
		$auth_headers = array();
	
		$key = array_search( 
			$HttpAuthRequest->scheme, array_map(
				'strtolower', $this->accepted_auth_schemes
			)
		);
			
		if( $key !== false && $this->accepted_auth_schemes[$key] !== null )
		{
			$identity_class = 'Http'.$this->accepted_auth_schemes[$key].'Identity';
			$HttpIdentity = new $identity_class( $HttpAuthRequest );

			if( $HttpIdentity->authenticate() )
				return $filterChain->run();

			$auth_headers = $HttpIdentity->makeAuthenticateHeader();
		}

		$Response = new Response();
		$Response->send( 
			401, 'Not Authorized', 'txt', $auth_headers
		);
	}

}

?>
