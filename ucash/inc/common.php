<?php 
namespace Includes\Common;
use Config\Config;
//** add custom class /functions here **/
	

class Sanitizer {

	public function sanitize_get(){
		$_GET = filter_var_array($_GET,FILTER_SANITIZE_STRING);
	}

	public function sanitize_post(){
		$_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);
	}

	public function sanitize_session(){
		$_SESSION = filter_var_array($_SESSION,FILTER_SANITIZE_STRING);
	}

	public function sanitize_all(){
		$_SESSION = filter_var_array($_SESSION,FILTER_SANITIZE_STRING);
		$_GET = filter_var_array($_GET,FILTER_SANITIZE_STRING);
		$_POST = filter_var_array($_POST,FILTER_SANITIZE_STRING);
	}


}


class NonceGenerator {

	public function generate(){
		if(!session_id()){
			session_start();
		}
		$characters = '01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characterlen = strlen( $characters );
		$stringbuilder = '';
		for( $i = 0; $i < $characterlen; $i++ ){
			$stringbuilder .= $characters[rand( 0, $characterlen - 1 )];
		}
		if(!isset($_SESSION['nonce'])){
			$_SESSION['nonce']=$stringbuilder;
		} 
	} 

	public function get(){
		if(!session_id()){
			session_start();
		}
		if(!isset($_SESSION['nonce'])){
			NonceGenerator::generate();
		}
		return $_SESSION['nonce'];
	}
}

class Network {
	public function ping($host, $port, $timeout) { 
	  $tB = microtime(true); 
	  $fP = @fSockOpen($host, $port, $errno, $errstr, $timeout); 
	  if (!@$fP) { @fclose($fP); return "down"; } 
	  $tA = microtime(true); 
	  @fclose($fP);
	  return round((($tA - $tB) * 1000), 0)." ms"; 
	}
}

?>