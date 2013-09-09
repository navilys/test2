<?php

class Webservices {       
	
	protected $bouchonWs;
    protected $url;
	protected $wsId;
	protected $inputParams;
	protected $errors;
	protected $failureCode;
	
	public function __construct() { 
    }
	
	public function _call() {
	
		//BUILD StreamContent
		$streamContent = $this->buildInputXML($this->inputParams);		
		
		// INIT stream params
		$params = array(
			"https" => array(
				"method" => "POST",
				"content" => $streamContent
			)
		);
		
		// ADD optionnal header
		if(!empty($this->header))
			$params['https']['header'] = $this->header;
			
		// BUILD And OPEN Stream
		try{
		//$ctx = stream_context_create($params);
		//var_dump($this->url);
		//var_dump($params);
		//$handle = fopen("https://extranet.aqoba-preprod.customers.artful.net/api/v09/solde?access_token=99ac21619656c825e788ffb8ac6bfa23f08f4b08", "r");
		//var_dump(@stream_get_contents($handle));
		phpinfo();
		exit(0);
		//$fp = @fopen("www.google.fr",'rb',false,$ctx);  
		//var_dump($fp);		
		//CHECK Stream
		if(!$fp)
			throw new Exception("Erreur Stream");			
		//GET Response
		$response = @stream_get_contents($fp);	
		}catch(Exception $e){
			var_dump($e);
		}
		
		$response = $this->bouchonWs;
		
		// CHECK Return
		try{
			$this->checkReturn($response);
		} catch (Exception $e) {
			throw $e;				
		}
		
		// GET Return value        
        return $response;

    }
	
	private function buildInputXML($params){
	
		// BUILD dom
		$dom = new DomDocument();

		// ******* ROOT *******
		$nodeRequest = $dom->createElement("request");
		
		// ******* HEAD *******
		$nodeHead = $dom->createElement("head");
		$nodeWsId = $dom->createElement("wsId");
		$nodeWsIdValue = $dom->createTextNode($this->wsId);		
		$nodeWsId->appendChild($nodeWsIdValue);
		$nodeHead->appendChild($nodeWsId);
		$nodeRequest->appendChild($nodeHead);		
		
		// ******* BODY *******
		$nodeBody = $dom->createElement("body");
		if($this->operation){
			$nodeOperation = $dom->createElement("operation");	
			$nodeOperationValue = $dom->createTextNode($this->operation);
			$nodeOperation->appendChild($nodeOperationValue);	
		}
		$nodeArguments = $dom->createElement("arguments");	
		
		// BROWSE Params
		foreach($params as $key=>$param){
		
			// CREATE Node
			$nodeParam = self::createNodeParam($dom,$key,$param);			
			$nodeArguments->appendChild($nodeParam);
		
		}
		
		if($this->operation)
			$nodeBody->appendChild($nodeOperation);
		$nodeBody->appendChild($nodeArguments);
		$nodeRequest->appendChild($nodeBody);
		$dom->appendChild($nodeRequest);
		
		// RETURN
		return $dom->saveXML();
		
	
	}
	
	private function createNodeParam($dom,$key,$param){
	
		// INIT
		$returnNode = $dom->createElement($key);	
		$nodeValue = $param["value"];
			
		// SET Value
		if(!empty($param["value"])){
		
			// BUILD Text Node
			$returnNodeValue = $dom->createTextNode($param["value"]);
			$returnNode->appendChild($returnNodeValue);	
		
		}elseif(!empty($param["children"])){
			
			foreach($param["children"] as $newParam){
				$newKey = key($newParam);
				$returnNode->appendChild(self::createNodeParam($dom,$newKey,$newParam[$newKey]));	
			}		
		
		
		}
		
		// ADD Attributes
		if($param['attr'] && is_array($param['attr']) && !empty($param['attr'])){
		
			foreach($param['attr'] as $attrName=>$attrValue){
				
				$returnNode->setAttribute($attrName,$attrValue);	
			}
		
		}
		
		// RETURN
		return $returnNode;
	
	}
    
    private function checkReturn($retour){
	
		// BUILD dom
		$dom = new DomDocument();
		
		//LOAD
		$dom->loadXML($retour);	
		$reponse = new SimpleXMLElement($dom->saveXML());
		
		//CHECK Return Code
		if(empty($reponse->status)){
		
			throw new Exception('Status introuvable dans la réponse XML', 1);
			
		}elseif(empty($reponse->status->success)){
		
			if(!empty($reponse->status->failure)){
			
				foreach($reponse->status->failure->errors->field as $field)
					$this->errors[] = array("field" => $field['name'],"desc" => $field);
					
				$this->failureCode = $reponse->status->failure->errors->code;
	
				throw new Exception('Status en echec dans la réponse XML.', 1);
				
			}else{
				throw new Exception('Status inconnu dans la réponse XML', 1);
			}
		}

		// RETURN
		return true;		
	
	}
}
