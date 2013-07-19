 <?php 
  try {  	
  $form = $_POST['form'];
  $ProUid = $form['PRO_UID'];
  $DocUid = $form['DOC_UID'];
  $DocKtTypeId = $form['DOC_KT_TYPE_ID'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

  require_once ( "classes/model/KtDocType.php" );

     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) {
       $tr = new KtDocType();
     }
     $tr->setProUid( $ProUid );
     $tr->setDocUid( $DocUid );
     $tr->setDocKtTypeId( $DocKtTypeId );

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
  G::Header('location: ktDocTypeList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   