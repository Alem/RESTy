RESTy - A RESTful Yii Extension
===============================

DESCRIPTION
---------------

RESTy enhances the Yii framework with functionality for simple, extendible, 
configurable, and intuitive RESTful API support.

RESTful API is provided through the creation of subclasses of the RestController 
component. These subclasses reside in their own sub-directory within the normal controllers/
directory (e.g. controllers/api/ ).

HTTP requests to these controllers are routed by their HTTP request method 
to the controller method matching the naming convention REQUEST METHOD+CLASS METHOD().

### Example REST Routing

The following are examples of this routing form 
(assume default REST controller directory 'api/'):

	GET     example.com/index.php/api/user/              UserController->get();
	GET     example.com/index.php/api/user?name=blah     UserController->get(); (with GET parameter name)
	GET 	example.com/index.php/api/user/lookup        UserController->getLookup();
	POST    example.com/index.php/api/user/              UserController->post();
	DELETE  example.com/index.php/api/user/              UserController->delete();
	
### HTTP Authentication

RESTy includes HTTP authentication support using the Basic frameworks.
These are managed by the HTTP[auth type]Identity components. The type of authentication
required by a RestController subclass is determined by the RestController property 'accepted_auth_schemes'.

Client Authorizing using Basic
	
	...Typical request headers...
	Authorization: Basic [username:password encoded in base64]
	
Client Authorizing using Unencoded (not recommended, may corrupt header):

	...Typical request headers...
	Authorization: Unencoded username:password

<!--
Client Authorizing using Digest [Not Supported Yet]

	...Typical request headers...
	 Authorization: Digest username="Mufasa",
                 realm="testrealm@host.com",
                 nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",
                 uri="/dir/index.html",
                 qop=auth,
                 nc=00000001,
                 cnonce="0a4f113b",
                 response="6629fae49393a05397450978507c4ef1",
                 opaque="5ccc069c403ebaf9f0171e9517f40e41"
-->

#### General Tip

This extension, and method of providing an API, result another set of controllers.
A simple way of reducing the development required to support both sets of controllers 
is to have your application models be as controller/implementation-neutral as possible.


SETUP
---------------

1. Place source code into protected/extensions/
2. Insert the following lines into your main.php configuration file.


        <?php
          ...
          'import'  =>  array(
                'application.extensions.resty.components.*',
                'application.extensions.resty.models.*',
        ),

        'components'=>array(
                ...
                'urlManager'=>array(
                        ...
                        'urlFormat'=>'path',
                        'rules'=>array(
                                array( 'class'  =>'application.extensions.resty.components.RestUrlRule' ),
                        ...
                        ),
                ...
                ),
        ),
        ?>

3. Install the ApiUser table using the following command (make sure console config is setup to access your database):


        yiic  migrate  --migrationPath=application.extensions.resty.migrations


LICENSE
---------------

Copyright (c) 2012, Alem
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the AlemCode nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Alem BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
