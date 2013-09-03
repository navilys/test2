<?php

require_once dirname(__FILE__).'/Webservice.php';


class Identification extends Webservices {   

	
	public function __construct() { 
		parent::__construct(); 
		
		// INIT
       	$this->url = "";
		$this->wsId = "307";
		
		// GET Bouchon
		$bouchonFileName = dirname(__FILE__).'/bouchonIdentification.txt';
        $handle = fopen($bouchonFileName, "r");
		while (!feof($handle)) { 
			$this->bouchonWs .= fgets($handle, 4096); 
		}
		
	} 
    
	public function call(){	

        // INIT
		$this->opearion = "IDENTIFICATIONS";
        $this->inputParams = array();
		$this->inputParams["porteurId"]["value"] = $this->porteurId;
		$this->inputParams["telephone"]["value"] = $this->telephone;
		$this->inputParams["portable"]["value"] = $this->portable;
		$this->inputParams["email"]["value"] = $this->email;
		$this->inputParams["numcarte"]["value"] = $this->numCarte;
		
		// CALL Ws
		$res = $this->_call();		        
		
		// RETURN
		return $res->status->success;

    } 
	
}


