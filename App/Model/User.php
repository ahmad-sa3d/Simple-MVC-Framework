<?php
namespace App\Model;

use App\Library\Model;

class User extends Model{
	
	protected static $table_name = 'users';
	protected static $class = __CLASS__;
	protected $hidden = [ 'password' ];
	protected $casts = [ 'admin' => 'boolean' ];
	
	protected static $db_fields = [
			'id',
			'username',
			'email',
			'password',
			'created_at',
			'updated_at',
			'admin',
		];
}