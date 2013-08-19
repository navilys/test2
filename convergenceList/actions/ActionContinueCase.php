
<?php 
G::loadClass ( 'pmFunctions' );
G::LoadClass("case");

// Execute events
require_once 'classes/model/Event.php';
require_once 'classes/model/AppDelay.php';
require_once 'classes/class.wsResponse.php';
$caseInstance = new Cases ();
$eventInstance = new Event();

//cleaning the case session data
//Cases::clearCaseSessionData();

$newFields = $caseInstance->loadCase ($_REQUEST['APP_UID']);
$newFields['APP_DATA']['FLAG_ACTION'] = 'actionCreateCase';
$newFields['APP_DATA']['FLAGTYPO3'] = 'Off';
PMFSendVariables($_REQUEST['APP_UID'], $newFields['APP_DATA']);		    
$caseInstance->updateCase($_REQUEST['APP_UID'], $newFields);

//G::pr($_REQUEST);
//proceed and try to open the case
$oAppDelegation = new AppDelegation();
$aDelegation = $oAppDelegation->load( $_REQUEST['APP_UID'], $_REQUEST['INDEX'] );
//G::pr($_SESSION['USER_LOGGED']);
if($aDelegation['USR_UID'] != $_SESSION['USER_LOGGED'] && $aDelegation['USR_UID'] != "")
{
	$_SESSION['APPLICATION'] = $_REQUEST['APP_UID'];
	$_SESSION['INDEX'] = $_REQUEST['INDEX'];
	require_once (PATH_METHODS . 'cases' . PATH_SEP . 'cases_Resume.php');
	    exit();
}
else 
{
    $swMessagge = 1;
	if ($aDelegation['USR_UID'] == "") 
	{ 
		$_SESSION['APPLICATION'] = $_REQUEST['APP_UID'];
		$_SESSION['INDEX'] = $_REQUEST['INDEX'];
		if ($caseInstance->isSelfService( $_SESSION['USER_LOGGED'], $_REQUEST['task'], $_SESSION['APPLICATION'] )) 
		{
        	//require_once (PATH_METHODS . 'cases' . PATH_SEP . 'cases_CatchSelfService.php');
        	$queryUpdateDelegation="UPDATE APP_DELEGATION SET USR_UID='".$_SESSION['USER_LOGGED']."' WHERE APP_UID='".$_REQUEST['APP_UID']."' AND DEL_INDEX='".$_REQUEST['INDEX']."' ";
			$resultDelegation = executeQuery($queryUpdateDelegation);
		}
		else 
		{
			require_once (PATH_METHODS . 'cases' . PATH_SEP . 'cases_Resume.php');
	   	    die;
		}      
	}
	$_SESSION['APPLICATION'] = $_REQUEST['APP_UID'];
	$_SESSION['INDEX'] = $_REQUEST['INDEX'];
	$_SESSION['PROCESS'] = $_REQUEST['PRO_UID'];
	$_SESSION['TASK'] = $_REQUEST['task'];
	$_SESSION['STEP_POSITION'] = 0;
    // Redirect to cases steps
    $query = "SELECT * FROM PMT_USER_CONTROL_CASES WHERE APP_UID = '".$_REQUEST['APP_UID']."' AND USR_UID != '".$_SESSION['USER_LOGGED']."'  ";
	$dataUsrCase = executeQuery($query);
    $eventInstance->createAppEvents($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);
	$nextStep = $caseInstance->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
	$nextPage = $nextStep['PAGE'] ;
    if(sizeof($dataUsrCase) > 0)
	{
	    $swMessagge = 0;
        $nextPage = $nextStep['PAGE'] ;
        ?>
        <link href="/plugin/convergenceList/convergenceList.css" rel="stylesheet" type="text/css" media="screen" />
        <script languaje='javascript'>
           var nextStep = <?php echo "'$nextPage'"?>;
               function closeMessagge(){
                   window.location.href = "../../cases/" + nextStep;
               }
        </script>
       
        <? 
        //G::SendTemporalMessage ("Une autre personne est en train d&#39;&eacute;diter cet enregistrement. Voulez-vous quand m&#233;me l&#39;&eacute;diter ?", "warning");
        $messageCases = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    
                         <div id='window-messagge'>
                        
                        <div id='container'>
                        <div class='contentMessage'>
                            Une autre personne est en train d&#39;&eacute;diter cet enregistrement. Voulez-vous quand m&#233;me l&#39;&eacute;diter ?
                       </div>
                       <a class='close' href='javascript:void(0);' onclick='closeMessagge()'>Continue</a>
                       </div>
                       </div>";
       echo $messageCases;
       $swCase = 1;
    }
	
    if($swMessagge == 1)
	    G::header('Location: ../../cases/' . $nextStep['PAGE']);
	
}

?>
