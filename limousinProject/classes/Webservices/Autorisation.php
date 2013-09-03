<?php

require_once dirname(__FILE__).'/Webservice.php';


class Solde extends Webservices {   

	
	public function __construct() { 
		parent::__construct(); 
		
		// INIT
       	$this->url = "";
		$this->wsId = "306";
		
		// GET Bouchon
		$bouchonFileName = dirname(__FILE__).'/bouchonAutorisation.txt';
        $handle = fopen($bouchonFileName, "r");
		while (!feof($handle)) { 
			$this->bouchonWs .= fgets($handle, 4096); 
		}
		
	} 
    
	public function call(){	

        // INIT
        $this->inputParams = array();
		$this->inputParams["partenaire"]["value"] = $this->partenaire;
		
		// CALL Ws
		$res = $this->_call();		        
		
		// RETURN
		return $res->status->success->list;

    } 
	
}


