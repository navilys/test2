 <?php 
  try {  	

    $form = $_POST['form'];
    $UsrUid = $form['USR_UID'];

  require_once ( PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'class.knowledgeTree.php');
  $pluginObj = new knowledgeTreeClass ();

    require_once ( "classes/model/KtConfig.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = KtConfigPeer::retrieveByPK( $UsrUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'KtConfig' ) ) {
      $tr->delete();
    }

    G::Header('location: ktConfigList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   