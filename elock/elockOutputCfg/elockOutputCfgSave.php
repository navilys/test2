 <?php 
  try {  	
  $form = $_POST['form'];
  $StpUid = $form['STP_UID'];
  $ProUid = $form['PRO_UID'];
  $TasUid = $form['TAS_UID'];
  $DocUid = $form['DOC_UID'];

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockOutputCfg.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = ElockOutputCfgPeer::retrieveByPK( $StpUid );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'ElockOutputCfg' ) ) {
       $tr = new ElockOutputCfg();
     }
     $tr->setStpUid( $StpUid );
     $tr->setProUid( $ProUid );
     $tr->setTasUid( $TasUid );
     $tr->setDocUid( $DocUid );

     if ($tr->validate() ) {
       // we save it, since we get no validation errors, or do whatever else you like.
       $res = $tr->save();
     }
     else {
       // Something went wrong. We can now get the validationFailures and handle them.
       $msg = '';
       $validationFailuresArray = $tr->getValidationFailures();
       foreach($validationFailuresArray as $objValidationFailure) {
         $msg .= $objValidationFailure->getMessage() . "<br/>";
       }
       //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
     }
    //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');

    //to do: uniform  coderror structures for all classes
  
  //if ( $res['codError'] < 0 ) {
  //  G::SendMessageText ( $res['message'] , 'error' );  
  //}
  //G::Header('location: elockOutputCfgList');   
  G::Header('location: ../../processes/processes_Map?PRO_UID='.$ProUid);
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   