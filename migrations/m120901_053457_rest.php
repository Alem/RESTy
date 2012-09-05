<?php
/**
 * This migration class borrows many ideas from 
 * http://code.google.com/p/yii-user/source/browse/trunk/modules/user/migrations/m110805_153437_installYiiUser.php?spec=svn131&r=131
 */
require(__DIR__.'/../models/ApiUser.php');

class m120901_053457_rest extends CDbMigration
{
	private $_model;
	public $api_user_table = 'tbl_api_users';

	public $default = array( 
		'username' => 'api_user', 
		'password' => 'api_key', 
		'email'=>'info@example.com' 
	);

	public function safeUp()
	{
		$this->createTable($this->api_user_table, array(
			"id" => "pk",
			"username" => "varchar(20) NOT NULL DEFAULT ''",
			"password" => "varchar(128) NOT NULL DEFAULT ''",
			"email" => "varchar(128) NOT NULL DEFAULT ''",
			"activation_key" => "varchar(128) NOT NULL DEFAULT ''",
			"createtime" => "int(10) NOT NULL DEFAULT 0",
			"lastvisit" => "int(10) NOT NULL DEFAULT 0",
			"privilege" => "int(1) NOT NULL DEFAULT 0",
			"status" => "int(1) NOT NULL DEFAULT 0",
		));

		if( in_array('--interactive=0',$_SERVER['argv'])) 
		{
			$this->_model->username = $this->default['username'];
			$this->_model->password = $this->default['password'];
			$this->_model->email 	= $this->default['email'];
		}
		else
		{
			$this->stdinToModel('First api user', 'username', $this->default['username'] );
			$this->stdinToModel('First user\'s password', 'password', $this->default['password'] );
			$this->stdinToModel('First user\'s email', 'email', $this->default['email'] );
		}

		$this->insert( $this->api_user_table, array(
			'id' => '1',
			'username' => $this->_model->username,
			'password' => md5($this->_model->password),
			'email' => $this->_model->email,
			'activation_key' => md5(microtime()),
			'createtime' => time(),
			'lastvisit' => '0',
			'privilege' => '1',
			'status' => '1',
		));
	}

	public function safeDown()
	{
		$this->dropTable($this->api_user_table);
	}

	private function stdinToModel($prompt, $field, $default = null ) 
	{
		if (!$this->_model)
			$this->_model = new ApiUser();
		do
		{
			if( $default !== null )
				$default_prmpt = "(default: $default)";
			else
				$default_prmpt = '';

			echo "$prompt $default_prmpt : ";

			$input = trim(fgets(STDIN));

			if( empty($input) && $default !== '' )
				$input = $default;

			$this->_model->setAttribute($field,$input);
		}
		while( !$this->_model->validate(array($field)) || empty($input) );

		return $input;
	}
}
