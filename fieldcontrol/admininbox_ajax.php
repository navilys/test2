<?php    
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', True );   
    G::LoadClass('case');
    G::LoadClass('configuration');
    G::loadClass('pmFunctions');
        
    $start = isset($_POST['start']) ? $_POST['start'] : 0;
    $limit = isset($_POST['limit']) ? $_POST['limit'] : 20;
        $USER_UID = $_SESSION['USER_LOGGED'];
	    $where="";
	    if (isset($_GET['inbox']))
	    {
	    	$inboxVar = $_GET['inbox'];
	    	switch ($inboxVar) {
	    		case 'listinbox':
					$sQuery = " SELECT * 
					       FROM PMT_INBOX
					       ";
					$aDatos = executeQuery ($sQuery);	    
					$array = Array();
					foreach($aDatos as $valor)
					{        
					$array[] = $valor;
					}
					$total = count($aDatos);


					header("Content-Type: text/plain");

					$paging = array(
					'success'=> true,
					'total'=> $total,
					'data'=> array_splice($array,$start,$limit)
					);  

					echo json_encode($paging);
					break;

	    		case 'newinbox':
	    		
	    		default:
	    			# code...
	    			break;
	    	}
	    } else {
	    	$action = $_POST['action'];
	    	switch ($action) {
	    		case 'saveNewInbox':
	    			
	    			$res = false;
	    			
			    	$inbox = $_POST['inbox'];
			    	$description = $_POST['desc'];
					$qVerification = "SELECT INBOX FROM PMT_INBOX WHERE INBOX = '$inbox'";
					$selectVerification = executeQuery($qVerification);
					
					if(count($selectVerification) == 0)
					{
						$sQuery = " INSERT INTO PMT_INBOX (INBOX, DESCRIPTION)
								VALUES ('$inbox', '".mysql_real_escape_string($description)."')
					       ";
						$aDatos = executeQuery ($sQuery);
						$res = true;
					}
					
					echo json_encode($res);
					
	    			break;
	    		
	    		case 'saveEditInbox':
	    			$inbox = $_POST['inbox'];
			    	$description = $_POST['desc'];
	    			$sQuery = " UPDATE PMT_INBOX 
					       SET DESCRIPTION = '".mysql_real_escape_string($description)."'
					       WHERE INBOX = '$inbox'
					       ";
					$aDatos = executeQuery ($sQuery);	    
					$res = true;
					header("Content-Type: text/plain");

					$paging = array('success' => $res );  

					echo json_encode($paging);
					break;
					
	    		case 'deleteinbox':
			    	$id = $_POST['ID'];
					$idInbox = $_POST['ID_INBOX'];
					
			    	// Delete relation inbox Roles
					$qInboxRoles = "DELETE FROM PMT_INBOX_ROLES WHERE ID_INBOX = '$idInbox' ";
			    	$delInboxRoles = executeQuery($qInboxRoles);
			    	
			    	// Delete relation inbox actions
			    	$qInboxActions = "DELETE FROM PMT_INBOX_ACTIONS WHERE ID_INBOX = '$idInbox' ";
			    	$delInboxActions = executeQuery($qInboxActions);
			    	
					$sQuery = "DELETE FROM PMT_INBOX WHERE ID = $id ";
					$aDatos = executeQuery ($sQuery);	    
	    			break;

	    		default:
	    			# code...
	    			break;
	    	}
	    	
			$array = Array();
	    }
?>
