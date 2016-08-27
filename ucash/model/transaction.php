<?php
// Transaction Model
namespace Model;
class Transaction extends base {
	
	public $user_id = "";

	public $username = "";
	
	public $table = "user";

	public function __construct(){
		parent::__construct();
	}


}

class TransactionDAO extends baseDAO{

	//add addtional query functions here
	//add business logic here

	public function checklogin( $username, $password ){
		return $this->select()
				   ->where('username', $username)
				   ->where('passowrd', $password)
				   ->grab(new Transaction);

	}

	public function getByTransactionname($username){
		$result = $this->select()
				->where('username',$username)
				->grab(new Transaction);
		return $result;
	}

	public function getTransactions($fields = array(), $values = array(), $offset  = null, $limit = null, $join = null ){
		$this->select();
		if($offset !== null){
			$this->offset($offset);

		}
		if($limit !== null){
			$this->limit($limit);

		}
		if( count($fields) != count($values) ){
			return "Error: field and value array mismatch";
		}
		if( count($fields) > 0 ){
			foreach( $fields as $field ){
				$this->where( $field, $value );
			}
		} 
		return $this->grab(new Transaction);
		// return $this->select()
		// 		->where('id','!=',"''")
		// 		->grab(new Transaction);
	}


    public function transfer(){
    	///
    	
    }
}

?>