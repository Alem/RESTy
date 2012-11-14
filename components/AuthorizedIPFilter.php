<?php
/**
 * AuthorizedIPFilter class file
 *
 * @author Wildan Maulana <wildan.m@opentinklabs.com>
 * @copyright Copyright 2012, OpenThink Labs
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * Allows automated authentication of controller actions.
 */
class AuthorizedIPFilter extends CFilter
{
	/**
	 * Authorized IP Address 
	 *
	 */
	public $ip_authorizeds = array();

	/**
	 * Performs authorized IP filter
	 * before execution of a RestController method
	 *
	 * @param CFilterChain $filter 	The filter chain
	 */
	public function preFilter( $filterChain )
	{  
		$HttpAuthRequest = new HttpAuthRequest();
		$auth_headers    = array();
		$credentials     = explode(":",(base64_decode($HttpAuthRequest->params)));
		if( is_array($credentials) && count($credentials) > 0 )
		{
			$api_user = ApiUser::model()->findByAttributes(array("username"=>$credentials[0])) ;
			if($api_user) {
				  $ip_authorizeds = explode(",",$api_user->ip_authorized) ; 
				  if(in_array($_SERVER['REMOTE_ADDR'],$ip_authorizeds)) 
				  	return $filterChain->run(); 
			}				
		}

		$Response = new Response();
		$Response->send( 
			401, 'Not Authorized', 'txt', $auth_headers
		);
	}
}
?>
