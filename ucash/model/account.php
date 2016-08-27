<?php
// User Model
namespace Model;
class Account extends base {
	
	public $table = "uhmnl_accounts";

	public function __construct(){
		parent::__construct();
	}


}

class AccountDAO extends baseDAO{
	public function getByAccountId( $account_id ){
		return $this->select()
					->where('id', $account_id )
					->grab( new Account );
	}

	public function getByMobileNo( $mobile_no ){
		return $this->select()
					->where('mobile_no', $mobile_no)
					->grab( new Account );
	}
}

?>