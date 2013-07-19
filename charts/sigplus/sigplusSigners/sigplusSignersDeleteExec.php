 <?php 
  try {  	

    $form = $_POST['form'];

  require_once ( PATH_PLUGINS . 'sigplus' . PATH_SEP . 'class.sigplus.php');
  $pluginObj = new sigplusClass ();

    require_once ( "classes/model/SigplusSigners.php" ); 
 
    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = SigplusSignersPeer::retrieveByPK(  );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'SigplusSigners' ) ) {
      $tr->delete();
    }

    G::Header('location: sigplusSignersList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   