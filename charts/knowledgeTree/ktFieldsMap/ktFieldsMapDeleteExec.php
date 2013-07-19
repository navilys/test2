 <?php 
  try {  	

    $form = $_POST['form'];
    $DocKtTypeId = $form['DOC_KT_TYPE_ID'];
    $ProUid = $form['PRO_UID'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

    require_once ( "classes/model/KtFieldsMap.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtFieldsMapPeer::retrieveByPK( $DocKtTypeId, $ProUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtFieldsMap' ) ) {
      $tr->delete();
    }

    G::Header('location: ktFieldsMapList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   