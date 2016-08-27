<?php
// User Model
namespace Model;
class User extends base {
	
	public $user_id = "";

	public $username = "";
	
	public $table = "user";

	public function __construct(){
		parent::__construct();
	}


}

class UserDAO extends baseDAO{

	//add addtional query functions here
	//add business logic here

	public function checklogin( $username, $password ){
		return $this->select()
				   ->where('username', $username)
				   ->where('passowrd', $password)
				   ->grab(new User);

	}

	public function getByUsername($username){
		$result = $this->select()
				->where('username',$username)
				->grab(new User);
		return $result;
	}

	public function getallusers($offset = null, $limit = null){
		if($offset !== null){
			$this->offset($offset);

		}
		if($limit !== null){
			$this->limit($limit);

		}
		return $this->select()
				->where('id','!=',"''")
				->grab(new User);
	}



}

?>