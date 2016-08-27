<?php
// Transaction Model
namespace Model;
class Loan extends base {
	
	public $principal;

	public $noy;

	public $interest;

	public $total;

	public $income;

	public function __construct(){
		parent::__construct();
	}


}

class LoanDAO extends baseDAO{

	//add addtional query functions here
	//add business logic here

	public function loanRequest( $principal, $interest, $noy ){

		//do curl here;
		$curl = curl_init();
		$principal = $principal;//sanitize this
		$interest = $interest;//sanitize this
		$noy = $noy;//sanitize this
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/Loans/compute?principal=".$principal."&interest=".$interest."&noy=".$noy,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
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
		  $convertedresults = array();	
		  $response = json_decode( $response, true);
			foreach ($reponse as $key => $result) {
				$resultModel = get_class($model);

				$resultObject = new $resultModel;
				foreach($result as $key => $value){
					$resultObject->$key = $value;
				}
				$resultObject->table = "";
				$resultArray = ( array ) $resultObject;
				if($this->select !="" && $this->select !="*"){
					foreach(  $resultArray as $key => $val){
						if(!in_array($key, explode(",",$this->select) ) ){
							unset($resultArray[$key]);
						} else{
							$resultArray[$key] = $val;
						}
					} 
				}
				$convertedresults[]= (object) $resultArray;

			}
			return $convertedresults;
		}
	}


}

?>