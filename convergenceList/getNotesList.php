<?php        
        require_once ("classes/model/Content.php");
        G::LoadClass( 'case' );

        $appUid = $_GET['appUid'];                
        $case = new Cases();
        $caseLoad = '';

        if (!isset($_SESSION['PROCESS'])) {
            $caseLoad = $case->loadCase($appUid);
            $httpData->pro = $caseLoad['PRO_UID'];
        }    
        
        if(!isset($httpData->pro) || empty($httpData->pro) )
        {
            $proUid = $_SESSION['PROCESS'];
        } else {
            $proUid = $httpData->pro;
        }
        
        $tasUid = $_GET['tas'];        
        $usrUid = $_SESSION['USER_LOGGED'];


        $usrUid = isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : "";
        $appNotes = new AppNotes();
        $response = $appNotes->getNotesList( $appUid, '', 0, 100000 );        
        $content = new Content();
        $response['array']['appTitle'] = $content->load('APP_TITLE', '', $appUid, SYS_LANG);
        header("Content-Type: text/plain");
        echo G::json_encode($response['array']);        

?>