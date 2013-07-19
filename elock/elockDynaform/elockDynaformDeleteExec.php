 <?php 
  try {  	

    $form = $_POST['form'];
    $UidDynaform = $form['UID_DYNAFORM'];
    $UidApplication = $form['UID_APPLICATION'];

  require_once ( PATH_PLUGINS . 'elock' . PATH_SEP . 'class.elock.php');
  $pluginObj = new elockClass ();

    require_once ( "classes/model/ElockDynaform.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = ElockDynaformPeer::retrieveByPK( $UidDynaform, $UidApplication );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'ElockDynaform' ) ) {
      $tr->delete();
    }

    G::Header('location: elockDynaformList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   