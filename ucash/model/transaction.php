<?php
// Transaction Model
namespace Model;
class Transaction extends base {
	
	public $id;

	public $dest_mobile;

	public $source_mobile;
	
	public $table = "uhmnl_transactions";

	public function __construct(){
		parent::__construct();
	}


}

class TransactionDAO extends baseDAO{

	//add addtional query functions here
	//add business logic here

	public function getByTransactionname($username){
		return $this->select()
				->where('username',$username)
				->grab(new Transaction);
	}

	public function getTransactionById($id){
		return $this->select()
					   ->where('id', $id)
					   ->grab( new Transaction );
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
	}

	public function saveTempTransaction( $transaction ){
		return $this->save( $transaction );
	}

	public function updateTransaction( $transaction, $transaction_id ){

		return $this->where('id', $transaction_id)->update( $transaction );
	} 


	public function getLatestThirty( ){
		return $this->select()
			 ->limit(30)
			 ->order('transaction_date','desc')
			 ->grab( new Transaction);
	}
}

?>