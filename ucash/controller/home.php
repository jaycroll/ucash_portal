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
class home extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		$config = new Config;
		$vars['title']	= "Popcorn Framework";
		$vars['config'] = $config;
		$vars['frnt_flg'] = true;
		echo $this->load->view( 'home.index', $vars );	
	}
	// public function test( $vars ) {
	// 	session_start();
	// 	$config = new Config;
	// 	$product = (object) [];

	// 	$product->productCategory = 'High Grade';
	// 	$product->productItemCode = 'TOY-GDM-2867';
	// 	$product->productName = 'HG 1/144 にっぽん';

	// 	echo json_encode((array)$product, JSON_UNESCAPED_UNICODE);
	// }
}
?> 