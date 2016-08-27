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
class loan extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		$config = new Config;
		$vars['title']	= "uCASH Products";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();
		echo $this->load->view( 'product.index', $vars );	
	}


	public function getLoan( $vars ){

	}
}
