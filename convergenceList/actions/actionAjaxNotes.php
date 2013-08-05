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
  //  insertHistoryLogPlugin($APP_UID,$USR_UID,$CURRENTDATETIME,$VERSION,$NEWAPP_UID,$ACTION,$STATUT="")
    $sql = 'SELECT HLOG_STATUS FROM PMT_HISTORY_LOG WHERE HLOG_DATECREATED = (SELECT MAX(HLOG_DATECREATED) AS DCREATED FROM PMT_HISTORY_LOG WHERE HLOG_APP_UID = "'. $APP_UID ."' OR HLOG_CHILD_APP_UID = '". $APP_UID ."')";
    $res = executeQuery($sql);

    $CURRENTDATETIME=date('Y-m-d H:i:s');
    insertHistoryLogPlugin($appUid,$usrUid,$CURRENTDATETIME,'0','','Ajout d\'un commentaire',$res[1]['HLOG_STATUS']);

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