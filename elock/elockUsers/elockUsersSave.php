 <?php 
  try {  	
  $form = $_POST['form'];
  $UsrUsername = $form['USR_USERNAME'];
  $UsrPassword = $form['USR_PASSWORD'];

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

  require_once ( "classes/model/ElockUsers.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = ElockUsersPeer::retrieveByPK( $UsrUsername );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'ElockUsers' ) ) {
       $tr = new ElockUsers();
     }
     $tr->setUsrUsername( $UsrUsername );
     $tr->setUsrPassword( $UsrPassword );

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
  G::Header('location: elockUsersList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   