 <?php 
  try {  	

    $form = $_POST['form'];
    $ProUid = $form['PRO_UID'];
    $DocUid = $form['DOC_UID'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

    require_once ( "classes/model/KtDocType.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtDocTypePeer::retrieveByPK( $ProUid, $DocUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtDocType' ) ) {
      $tr->delete();
    }

    G::Header('location: ktDocTypeList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   