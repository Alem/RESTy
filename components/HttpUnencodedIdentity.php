<?php
/**
 * HttpBasicIdentity class file.
 *
 * @author Z. Alem <info@alemcode.com>
 * @copyright Copyright 2012, Z. Alem
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 * HttpUnencodedIdentity allows authentication of credentials passed
 * as unencoded plain text in an HTTP authentication header.
 *
 * This is not recommended as some characters present in usernames or passwords
 * may cause the HTTP header to be parsed incorrectly.
 *
 * @author Z. Alem <info@alemcode.com>
 */
class HttpBasicIdentity extends HttpIdentity
{
	public $www_auth = array(
		'auth_scheme' => 'Unencoded',
		'realm' => 'Restricted Area'
	);

	public $username = null;

	public $password = null;

}
?>
