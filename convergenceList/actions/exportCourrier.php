<?php 
G::loadClass ( 'pmFunctions' );

$getVar = array();

$getVar = json_decode($_REQUEST['idFile'],true);

if (count($getVar) == 1) {
    // ajout de AND CON_LANG = 'fr' car la requète trouve un doc avec le même app_uid dans chaque langue depuis la maj des traductions.
    $query = 'SELECT * FROM APP_DOCUMENT, CONTENT WHERE APP_UID="'.$getVar[0]['APP_UID'].'" AND APP_DOC_TYPE="OUTPUT" AND APP_DOC_STATUS="ACTIVE" AND APP_DOC_UID = CON_ID AND CON_CATEGORY = "APP_DOC_FILENAME"';
    $result = executeQuery($query);    
    if( count($result) == 1 && isset($result[1]['APP_DOC_UID'])) {

        $path = PATH_DOCUMENT . $getVar[0]['APP_UID'] . PATH_SEP . 'outdocs' . PATH_SEP . $result[1]['APP_DOC_UID'] . '_' . $result[1]['DOC_VERSION'];
        $file = file_get_contents($path.'.pdf');

        //OUPUT HEADERS
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/pdf");    
        header('Content-Disposition: attachment; filename="'.$result[1]['CON_VALUE'].'.pdf";' );
        header('Content-Length: '.strlen($file)); 
        header("Content-Transfer-Encoding: binary");

        echo $file;
    }
    else
        echo 'pas de fichier';
}
else {
    
    foreach($getVar as $item) {
        $files[]= "'".$item['APP_UID']."'";     
    }    
    $fileContent = convergence_concatFiles($files);
    
    //OUPUT HEADERS
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/pdf");    
    header('Content-Disposition: attachment; filename="courriers_'.time().'.pdf";' );
    header('Content-Length: '.strlen($fileContent)); 
    header("Content-Transfer-Encoding: binary");

    echo $fileContent;
    
}



?>