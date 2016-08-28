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
class accessAuth extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function login( $vars ) {
		session_start();
		Sanitizer::sanitize_post();
		$config = new Config;
		extract($this->load->model('User'));
		extract($_POST);
		if($nonce == $_SESSION['nonce']){

			$user = $UserDAO->checklogin($user, $pass);
			if( $user->username != null && $user->username != "" ){
					$_SESSION['username'] = $user->username;
					$_SESSION['account_id'] = $user->account_id;
					header('location:'.$config->http.'dashboard');
			}
		} else{
			header('Location:'.$config->http.'/?invalid_request');
		}
	}
	public function logout( $vars ){

	} 
}
