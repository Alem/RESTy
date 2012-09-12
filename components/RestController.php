<?php
/**
 * RestController class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
require( dirname(__DIR__).'/vendors/minimvc/Response.php');
/**
 * RestController provides the basic methods for a controller offering
 * REST services. 
 *
 * @author Z. Alem <info@alemcode.com>
 */
abstract class RestController extends Controller
{

	/** 
	 * Used to provide custom mappings for HTTP request methods
	 * For example, 'get'=>'gt'
	 * maps GET requests to gtMethod(), instead of getMethod().
	 */
	public $map_methods = array();

	/**
	 * Default content type header to send
	 * @see Response::supported_formats
	 */
	public $default_content_type = 'txt';

	/**
	 * Users must authenticate using HTTP Basic ( HttpBasicIdentity )
	 * for every API call
	 */
	public $require_auth = true;

	/** Mandate SSL/TLS encryption */
	public $require_ssl = false;

	/**
	 * Http Authentication type
	 *
	 * @see HttpAuthFilter::accepted_auth_schemes
	 */
	public $accepted_auth_schemes = array('Basic');

	/**
	 * Additional headers to add to every request.
	 *
	 *      e.g. array( 'Header1: Text', 'Header2: Text' )
	 */
	public $additional_headers = array();


	/**
	 * Provides common structure for routing verb specific 
	 * actions to local methods complying with verbActionID
	 * convention.
	 *
	 * If no match is found, calls {@link actionError() }.
	 *
	 * _Examples_ 
	 * * a GET request to api/Post/lookup calls api/PostController->getLookup()
	 * * a PUT request to api/Post calls api/PostController->put();
	 *
	 * @param string $action 	The action id. 
	 */
	public function actionRestRoute( $action = '' )
	{
		$request = new CHttpRequest();
		$verb 	= $request->getRequestType();

		if( isset($this->map_methods[$verb]) )
			$verb = $this->map_methods[$verb];

		$verb_action = $verb . $action;

		if( method_exists( $this, $verb_action ) )
			$this->$verb_action();
		else
			$this->actionError();
	}

	/**
	 * A 404 error message. 
	 */
	public function actionError()
	{
		$this->response(404, 'The requested resource could not be found.');
	}


	/**
	 * Registers filters to run
	 */
	public function filters()
	{
		$filters = array();
		
		if( $this->require_auth === true )
		{
			$filters[]= array( 
					'application.extensions.resty.components.HttpAuthFilter',
					'accepted_auth_schemes' =>$this->accepted_auth_schemes
			);
		}
		if( $this->require_ssl === true )
		{
			$filters[]= 'ssl';
		}

		return $filters;
	}

	/**
	 * Require SSL. Throw a 403.4 error if the connection is unencrypted.
	 */
	public function filterSsl( $filterChain )
	{
			$request = new CHttpRequest();
			if( $request->getIsSecureConnection())
				$filterChain->run();
			else
			{
				$response = new Response();
				$response->status_codes[403.4] = 'Forbidden: SSL required';
				$body = 'The resource you requested requires SSL, but your request was made through unencrypted HTTP.';
				$response->send(403.4, $body, 'txt' );
			}
	}

	/**
	 * Sends HTTP header and body.
	 *
	 * @param integer $status_code 	The numeric HTTP status code
	 * @param mixed   $body 	The data comprising the response body
	 * @param string  $content_type The content type of the response data
	 * 				Defaults to {@link Response::content_type}
	 * @param string  $more_headers Optional additonal headers
	 */
	public function response( $status_code = 200, $body = '', $content_type = null, $more_headers = array() )
	{
		$response = new Response();
		$response->content_type = $this->default_content_type;

		$more_headers = $more_headers + $this->additional_headers; 

		$response->send( $status_code, $body, $content_type, $more_headers );
	}
}
?>
