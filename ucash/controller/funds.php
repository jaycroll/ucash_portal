<?php 
/*
	DO NOT PUT ANY BUSINESS LOGIC IN HERE! THAT GOES TO THE MODEL DAO CLASSES!
	CONTROLLERS ARE JUST FOR CONTROLLING NOT PROCESSING BUSSINESS LOGIC!
*/
//use Philo\Blade\Blade;
namespace Controller;
use Loader;
use Includes\Error;
use Includes\Common\Sanitizer as Sanitizer;
use Includes\Common\NonceGenerator as Nonce;
use Includes\Crypt\Salt as Salter;
use Config\Config;
use Model;
class Funds extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		// var_dump($_SESSION);
		extract($this->load->model('transaction'));
		if( !isset( $_SESSION['username'] ) ){
			header('Location:/?forbidden');
		}
		$config = new Config;
		$vars['title']	= "Funds";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();
		$vars['user'] = $this->getAccount( $_SESSION['username'] );
		$vars['transactions'] = $transactionDAO->getLatestThirty();
		echo $this->load->view( 'funds.index', $vars );	
	}

	public function transfer( $vars ){
		session_start();
		
		// var_dump($_SESSION);
		if( !isset( $_SESSION['username'] ) ){
			header('Location:/?forbidden');
		}
		
		if( isset($_POST['submit']) ){
			if( $_SESSION['nonce'] == $_POST['nonce'] ){
				Sanitizer::sanitize_post();
				$this->sendTransfer( array_map('trim', $_POST ) );
			} else{
				header('Location: '.$config->http.'/funds/transfer/?invalid_request');
			}
		} else{
			Nonce::refresh();
			$config = new Config;
			$vars['title']	= "Funds - Transfer";
			$vars['config'] = $config;
			$vars['nonce'] = Nonce::generate();
			
			echo $this->load->view( 'funds.transfer', $vars );	
		}

	}

	private function getAccount( $username ){
	
		extract($this->load->model('user'));
		extract($this->load->model('account'));
		$user = $userDAO->getByUsername('admin');
		$user->account_details = $accountDAO->getByAccountId($user->account_id);
		$user->balance_details = $this->getBalanceDetails($user->account_id);
		return $user;
	}

	private function getBalanceDetails($account_no){
		$curl = curl_init();
		$account_no = Sanitizer::sanitize_string(trim( $account_no ));
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/getAccount?account_no=".$account_no,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTPHEADER => array(
		    "accept: application/json",
		    "content-type: application/json",
		    "x-ibm-client-id: bfac49db-0569-412d-925b-263b3e640c4c",
		    "x-ibm-client-secret: uU0vR1hC1bT6xI3cP1jD3uI1jW6cK0nG0iS4fT6vO8mL2xL6kJ"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  	$response = json_decode($response);
		  	if( count($response) < 2 ){
		  		$response = $response[0];
		  	}
		  	return $response;
		}
	} 

	private function sendTransfer( $vars ){
		$curl = curl_init();
		$config = new Config;
		$channel_id = "UHACK_0028";
		extract( $this->load->model( "transaction" ) );
		extract( $this->load->model( "account" ) );
		$account_id_from = $_SESSION['account_id'];
		$account_id_to = $accountDAO->getByMobileNo($vars['mobile_no'])->id;
		$transaction->type = "stok";
		$transaction->dest_mobile = $vars['mobile_no'];
		$transaction->source_mobile = $accountDAO->getByAccountId($account_id_from)->mobile_no;
		$transaction->amount = $vars['amount'];
		// var_dump($transaction);
		$transaction_id = $transactionDAO->save($transaction, true);
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/transfer",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
  		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\"channel_id\":\"".$channel_id."\",\"transaction_id\":\"".$transaction_id."\",\"source_account\":\"".$_SESSION['account_id']."\",\"source_currency\":\"php\",\"target_account\":\"".$account_id_to."\",\"target_currency\":\"php\",\"amount\":".$vars['amount']."}",
		  CURLOPT_HTTPHEADER => array(
		    "accept: application/json",
		    "content-type: application/json",
		    "x-ibm-client-id: bfac49db-0569-412d-925b-263b3e640c4c",
		    "x-ibm-client-secret: uU0vR1hC1bT6xI3cP1jD3uI1jW6cK0nG0iS4fT6vO8mL2xL6kJ"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  $response = json_decode($response);
		  $transaction->id = "";
		  if( $response->status != "S" || $response->error_message != ""){
		  	// header("Location:".$config->http."funds/transfer/?error=".$response->error_message);
		  } else{
		  		$transaction->reference = $response->confirmation_no;
		  		
		  		$update = $transactionDAO->updateTransaction( $transaction, $transaction_id );
		  		$transaction = $transactionDAO->getTransactionById($transaction_id);
		  		
		  		Nonce::refresh();
				$config = new Config;
				$vars['title']	= "Funds - Transfer";
				$vars['config'] = $config;
				$vars['nonce'] = Nonce::generate();
				$vars['account'] = $accountDAO->getByMobileNo($vars['mobile_no']);
				echo $this->load->view( 'funds.transfer', $vars );	
		  }
		  
		}
	}

	function generateRandomString($length = 10) {
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}
