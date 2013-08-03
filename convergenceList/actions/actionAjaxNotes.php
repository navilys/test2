<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");
header ( "Content-Type: text/plain" );
$array=array();

function FpostNote($appUid,$noteText)
 {    

    $usrUid = (isset($_SESSION['USER_LOGGED'])) ? $_SESSION['USER_LOGGED'] : "";
    require_once ( "classes/model/AppNotes.php" );

    $appNotes = new AppNotes();
    $noteContent = addslashes($noteText);

    $result = $appNotes->postNewNote($appUid, $usrUid, $noteContent, false);    

}
$APP_UID = $_REQUEST['APP_UID'];
$Note = $_REQUEST['Note'];
// FpostNote($APP_UID,$Note);
$results = FpostNote($APP_UID,$Note);
if ($results['success'] = 'success') {
    $messageInfo = "La note a été ajouté avec succès";
} else {
    $messageInfo = "Erreur";
}
// $messageInfo = "La note a été ajouté avec succès!";
$paging = array ('success' => true, 'messageinfo' => $messageInfo);
echo G::json_encode ( $paging );
?>