<?php
/**
 * RestUrlRule
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * Maps URI requests to action RestRoute() of a RestController
 * subclass.
 *
 * @author Z. Alem <info@alemcode.com>
 */
class RestUrlRule extends CBaseUrlRule
{
	/** The RestController routing action/method */
	public $routing_method = 'RestRoute';

	/** Mandate SSL/TLS encryption */
	public $require_ssl = false;

	/** Where rest controllers reside */
	public $rest_controller_dir = 'api/';

	/**
	 * Parses a URL based on this rule. Matches calls to REST controllers
	 *
	 * If successful, the URL will be mapped to:
	 * 	
	 * 	api/<controller>/<RestRoutingMethod>/action/<action>/verb/<request_method>
	 *
	 * @param CUrlManager $manager the URL manager
	 * @param CHttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID or false on error
	 */
	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
	{
		if( $this->require_ssl && !$request->getIsSecureConnection())
			return false;

		if(preg_match('#^'.$this->rest_controller_dir.'#',$pathInfo,$matches))
		{
			// 0=>api, 1=>controller, 2=>action, 3=>params
			$uri_seg = explode( '/', $pathInfo, 4 );
			
			if( !isset( $uri_seg[1] ) )
				return false;

			$action = (isset($uri_seg[2])) ? $uri_seg[2] :'';

			if( isset( $uri_seg[3] ) )
			$manager->parsePathInfo( $uri_seg[3] );

			$verb = $request->getRequestType();

			return $this->rest_controller_dir.$uri_seg[1].'/'.$this->routing_method.'/verb/'.$verb.'/action/'.$action.'/'; 
		}
		else
			return false;
	}

	public function createUrl( $manager, $route, $params, $ampersand )
	{
		// This rule is for parsing only
		return false;
	}

}

?>
