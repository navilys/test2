<?php
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );
G::LoadClass ( 'case' );
G::LoadClass ( 'configuration' );
G::loadClass ( 'pmFunctions' );

function fieldControlGetPMGRALFunctions(){
	G::LoadClass ( 'triggerLibrary' );
	$triggerLibrary = triggerLibrary::getSingleton ();	
	$triggerLibraryO = $triggerLibrary->getRegisteredClasses ();
	$arraypmF = array();
	foreach ( $triggerLibraryO as $keyLibrary => $libraryObj ) {
			$libraryName = $libraryObj->info ['name'];	
			$triggerCount = count ( $libraryObj->methods );
			if ($triggerCount > 0) {
				ksort($libraryObj->methods,SORT_STRING);
				foreach ( $libraryObj->methods as $methodName => $methodObject ) {
					
					$methodName = $methodObject->info ['name'];
					$methodLabel = $methodObject->info ['label'];				
					$arraypmF[] = $methodName."()";
				}			
			}		
			
	}
	return $arraypmF;
}


$RBAC->requirePermissions('PM_SETUP_ADVANCE');

$start = isset ( $_POST ['start'] ) ? $_POST ['start'] : 0;
$limit = isset ( $_POST ['limit'] ) ? $_POST ['limit'] : 1000;

$array = Array ();
$arrayFunctions = Array ();
$items = Array();
$total = 0;
//***************** Plugins **************************
G::LoadClass('plugin');
if(isset($_GET ['plugin']) && $_GET ['plugin'] == 0)
{
	$aPluginsPP = array();
	if (is_file(PATH_PLUGINS . 'enterprise/data/data')) 
	{
		$aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . 'enterprise/data/data')));
    	foreach ($aPlugins as $aPlugin) {
      		$aPluginsPP[] = substr($aPlugin['sFilename'], 0, strpos($aPlugin['sFilename'], '-')) . '.php';
    	}
	}
	$oPluginRegistry =& PMPluginRegistry::getSingleton();
	if ($handle = opendir( PATH_PLUGINS  )) 
	{
		$total = 0;
		while ( false !== ($file = readdir($handle))) 
		{
			if ( strpos($file, '.php',1) && is_file(PATH_PLUGINS . $file) ) 
			{
         		include_once ( PATH_PLUGINS . $file );
         		$pluginDetail = $oPluginRegistry->getPluginDetails ( $file );
        
         		if($pluginDetail==NULL) continue; //When for some reason we gen NULL plugin
         		$status_label = $pluginDetail->enabled ? G::LoadTranslation('ID_ENABLED') : G::LoadTranslation('ID_DISABLED');
         		$status = $pluginDetail->enabled ? 1: 0; 
         		if($status === 1)
         		{
          			$plugin = explode('.',$file);
          			if($plugin[0] != 'enterprise')
          			{
         				$arrayPlugin =  array(
									"ID" => $plugin[0],
    								"NAME" => $plugin[0]
								);
		
						$array [] = $arrayPlugin;
						$total ++;
          			}
        		}
    		}
		}
		closedir($handle);
	//G::pr($array);
	}
}
elseif(isset($_REQUEST ['idPlugin']) && $_REQUEST ['idPlugin'] != '') 
{
	
	$rC = new ReflectionClass ( $_REQUEST ['idPlugin'].'Plugin' );
	$url = PATH_PLUGINS .'convergenceList/public_html/js/functionsActions.js';
	$urlPmFunctions = PATH_PLUGINS . $_REQUEST ['idPlugin'].'/classes/class.pmFunctions.php';
	$file = file ( $url );
	$lines = count ( $file );
	$filePmFunctions = file ( $urlPmFunctions );
	$linesPmFunctions = count ( $filePmFunctions );
	foreach ( $rC->getMethods () as $rM ) 
		{
			$parameters = '';
			foreach ( $rM->getParameters () as $par ) 
			{
				$parameters .= $par->name . ' ';
			}
   
			$arrayFunctions =  array(
				"ID" => $rM-> name,
    			"NAME" => $rM-> name.'()',
				"CLASS" => $rM-> class,
   	 			"PARAMETERS_FUNCTION" => $parameters,
			);
		
			$array [] = $arrayFunctions;
	
		}
		for($i = 0; $i<$lines; $i++)
		{
			$patron = '/^function /';
			$result = preg_match ( $patron, $file [$i], $matches, PREG_OFFSET_CAPTURE );
			if (count ( $matches ) != 0) 
			{	//G::pr($matches);
				$functionAux = explode('(',$file [$i]);
				$delFunction = explode (' ',$functionAux[0]);
				$function = $delFunction[1];
			
				$functionAux = explode(')',$functionAux[1]);
			
				if(count($functionAux))
				{
					$parameters = $functionAux[0];
					$parameters = str_replace(',','',$parameters);
				}
				else 
					$parameters = '';
				
				$arrayFunctions =  array(
	    			"ID" => $function,
	   	 			"NAME" => $function.'()',
					"CLASS" =>'',
					"PARAMETERS_FUNCTION"=>$parameters
				);
				$array []= $arrayFunctions;
				
			}
		}
		for($i = 0; $i<$linesPmFunctions; $i++)
		{
			$patron = '/^function /';
			$result = preg_match ( $patron, $filePmFunctions [$i], $matches, PREG_OFFSET_CAPTURE );
		
			if (count ( $matches ) != 0) 
			{
				$functionAux = explode('(',$filePmFunctions [$i]);
				$delFunction = explode (' ',$functionAux[0]);
				$function = $delFunction[1];
			
				$functionAux = explode(')',$functionAux[1]);
			
				if(count($functionAux))
				{
					$parameters = $functionAux[0];
					$parameters = str_replace(',','',$parameters);
				}
				else 
					$parameters = '';
			
				$arrayFunctions =  array(
	    			"ID" => $function,
	   	 			"NAME" => $function.'()',
					"CLASS" => '',
					"PARAMETERS_FUNCTION"=>$parameters
				);
				$array []= $arrayFunctions;
			
			}
		}
		$total = sizeof ( $rC->getMethods () );
	
	
}
header ( "Content-Type: text/plain" );
$paging = array ('success' => true, 'total' => $total, 'data' => array_splice ( $array, $start, $limit ) );
echo json_encode ( $paging );

?>