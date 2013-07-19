 <?php 
  try {  	
  $form = $_POST['form'];
  $UsrUid = $form['USR_UID'];
  $KtUsername = $form['KT_USERNAME'];
  $KtPassword = $form['KT_PASSWORD'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtConfig.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = KtConfigPeer::retrieveByPK( $UsrUid );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtConfig' ) ) {
       $tr = new KtConfig();
     }
     $tr->setUsrUid( $UsrUid );
     $tr->setKtUsername( $KtUsername );
     $tr->setKtPassword( G::encrypt($KtPassword,$UsrUid) );

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
       print "$msg";
     }
    //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');

    //to do: uniform  coderror structures for all classes
  
  //if ( $res['codError'] < 0 ) {
  //  G::SendMessageText ( $res['message'] , 'error' );  
  //}
 // G::Header('location: ktConfigList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   