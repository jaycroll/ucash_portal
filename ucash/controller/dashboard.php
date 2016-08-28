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
class Dashboard extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		// var_dump($_SESSION);
		if( !isset( $_SESSION['username'] ) ){
			header('Location:/?forbidden');
		}
		$config = new Config;
		$vars['title']	= "uCASH Portal";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();
		// extract($this->load->model(''));
		$vars['amount'] = $this->getBalanceDetails($_SESSION['account_id']);
		echo $this->load->view( 'dashboard.index', $vars );	
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
}
