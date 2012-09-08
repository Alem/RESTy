<?php
/**
 *
 */
class HttpAuthFilter extends CFilter
{
	public $accepted_auth_schemes = array();
	public $content_type = 'html';

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
				'strtolower', $this-accepted_auth_schemes
			)
		);
			
		if( $key !== false )
		{
			$identity_class = 'Http'.$this-accepted_auth_schemes[$key].'Identity';
			$HttpIdentity = new $identity_class( $HttpAuthRequest );

			if( $HttpIdentity->authenticate() )
				return $filterChain->run();

			$auth_headers = $HttpIdentity->makeAuthenticateHeader();
		}

		$Response = new Response();
		$Response->send( 
			'401', "Not authorized", 
			$this->default_content_type, $auth_headers
		);
	}

	public function postFilter( $filterChain )
	{
	}
}

?>
