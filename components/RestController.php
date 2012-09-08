<?php
/**
 * RESTController class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
require( __DIR__.'/../vendors/minimvc/Response.php');
/**
 * RESTController provides the basic methods for a controller offering
 * REST services. 
 *
 * @author Z. Alem <info@alemcode.com>
 */
abstract class RESTController extends Controller
{

	/** 
	 * Used to provide aliases for HTTP request methods
	 * For example, 'get'=>'gt'
	 * maps GET requests to GT<method>()
	 */
	public $method_alias = array();

	/**
	 * Default content type header to send
	 */
	public $default_content_type = 'html';

	/**
	 * Users must authenticate using HTTP Basic ( HttpBasicIdentity )
	 * for every API call
	 */
	public $require_auth = true;

	/**
	 * Http Authentication type
	 *
	 * Options: 
	 * - 'Basic' for Basic Authentication, 
	 * - 'Digest' for Digest Authentication
	 */
	public $accepted_auth_schemes = array('Basic');

	/**
	 * Provides common structure for routing verb specific 
	 * actions to local methods complying with verbActionID
	 * convention.
	 *
	 * _Examples_ 
	 * * a GET request to api/Post/lookup calls api/PostController->getLookup()
	 * * a PUT request to api/Post calls api/PostController->put();
	 *
	 * @param string $action 	The action id. 
	 */
	public function actionRestRoute( $action = null )
	{
		$request = new CHttpRequest();
		$verb 	= $request->getRequestType();

		if( isset($this->method_alias[$verb]) )
			$verb = $this->method_alias[$verb];

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
		$this->response('404', 'The requested resource could not be found.');
	}


	/**
	 * Registers filters to run
	 */
	public function filters()
	{
		if( $this->require_auth === true )
		{
			return array(
				array( 
				'application.extensions.resty.components.HttpAuthFilter',
					'accepted_auth_schemes' =>$this->accepted_auth_schemes,
					'default_content_type' =>$this->default_content_type,
				)
			);
		}
		else 
			return array();
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
		$response->send( $status_code, $body, $content_type, $more_headers );
	}
}
?>
