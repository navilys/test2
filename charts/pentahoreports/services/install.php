<?php
  /**
   * install.php
   * Installation script that generates the configuration settings and files in order to install the plugin in a new workspace
   * @author Colosa Development Team 2010
   * @package plugins.pentahoreports.services
   * @Date 17/05/2010
   */
  require_once ( PATH_PLUGINS . "pentahoreports/class.pentahoreports.php" );
  $objPentaho = new pentahoreportsClass();
  $objPentaho->readLocalConfig( SYS_SYS );
  
  /**
   * This option creates the database tables and also adds the trigger into the Application and App_Delegation Tables
   */
  try {
    $res = $objPentaho->setupDatabase();
    if ( $res == 1 ) 
      print "Table APP_CACHE_VIEW and triggers are installed successfully";
    else
      throw ( new Exception ( $res ) );
  }
  catch (Exception $e ) {
    print "<font color='red'>" . $e->getMessage() . "</font>";
  }
  print "</br>";

  /**
   * This option creates the pentaho user in the pentaho server
   */
  try {
    $res = $objPentaho->testPentahoUser();
  }
  catch (Exception $e ) {
    print "<font color='red'>" . $e->getMessage() . "</font>";
  }

  /**
   * This option creates the pentaho user in the pentaho server
   */
  try {
    $result = $objPentaho->getSolutionRepository();
  
    if ( is_array($result) && $result[0]['name'] == SYS_SYS && isset( $result[0]['files'] ) && count ( $result[0]['files'] ) > 10 ) {
      print "workspace already synchronized with Pentaho Solution";
    }
    else {
      $result = $objPentaho->createNewSolutionWorkspace( SYS_SYS  );
      if ( $result == true ) 
        print "workspace synchronized with Pentaho Solution";
      else
        throw ( new Exception ( $res ) );
    }
  }
  catch (Exception $e ) {
    print "<font color='red'>" . $e->getMessage() . "</font>";
  }
  print "</br>";

  /**
   * This option recreates the pentaho repository.
   */
  try {
    $result = $objPentaho->getSolutionRepository();

    $result = $objPentaho->createNewSolutionWorkspace( SYS_SYS  );
    if ( $result == true ) 
      print "workspace synchronized with Pentaho Solution";
    else
      throw ( new Exception ( $res ) );
  }
  catch (Exception $e ) {
    print "<font color='red'>" . $e->getMessage() . "</font>";
  }
  print "</br>";


  /**
   * This option creates the jndi data source in the pentaho server
   */
  try {
    $res = $objPentaho->createJndi ();
  }
  catch (Exception $e ) {
    print "<font color='red'>" . $e->getMessage() . "</font>";
  }
  print "</br>";
  
  
