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
		$vars['title']	= "uCASH - Loan Application";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();
		echo $this->load->view( 'application.index', $vars );	
	}


	public function apply( $vars ){
		session_start();
		if(isset( $_POST['submit'])){
			$result = array();
			$result[] = $this->getLoan(1, $_POST['principal'], 3);
			$result[] = $this->getLoan(3, $_POST['principal'], 3);
			$result[] = $this->getLoan(5, $_POST['principal'], 3);
			$vars['result'] = $result;
		} Nonce::refresh();
			$config = new Config;
			$vars['title']	= "uCASH - Loan Application";
			$vars['config'] = $config;
			$vars['nonce'] = Nonce::generate();
			echo $this->load->view( 'application.apply', $vars );
	}

	private function getLoan($noy, $principal, $interest ){
		extract($this->load->model('loan'));
		return $loanDAO->loanRequest($principal, $interest, $noy)[0];
	}
}
