 <?php 
  try {  	
  $form = $_POST['form'];
  $DocKtTypeId = $form['DOC_KT_TYPE_ID'];
  $ProUid = $form['PRO_UID'];
  $FieldsMap = $form['FIELDS_MAP'];
  $DestinationPath = $form['DESTINATION_PATH'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtFieldsMap.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = KtFieldsMapPeer::retrieveByPK( $DocKtTypeId, $ProUid );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) {
       $tr = new KtFieldsMap();
     }
     $tr->setDocKtTypeId( $DocKtTypeId );
     $tr->setProUid( $ProUid );
     $tr->setFieldsMap( $FieldsMap );
     $tr->setDestinationPath( $DestinationPath );

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
  G::Header('location: ktFieldsMapList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   