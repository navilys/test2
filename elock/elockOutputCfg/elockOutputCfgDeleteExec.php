 <?php 
  try {  	

    $form = $_POST['form'];
    $StpUid = $form['STP_UID'];

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

    require_once ( "classes/model/ElockOutputCfg.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ElockOutputCfgPeer::retrieveByPK( $StpUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockOutputCfg' ) ) {
      $tr->delete();
    }

    G::Header('location: elockOutputCfgList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   