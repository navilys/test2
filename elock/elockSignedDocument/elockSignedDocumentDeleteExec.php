 <?php 
  try {  	

    $form = $_POST['form'];
    $AppDocUid = $form['APP_DOC_UID'];
    $DocVersion = $form['DOC_VERSION'];

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

    require_once ( "classes/model/ElockSignedDocument.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ElockSignedDocumentPeer::retrieveByPK( $AppDocUid, $DocVersion );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockSignedDocument' ) ) {
      $tr->delete();
    }

    G::Header('location: elockSignedDocumentList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   