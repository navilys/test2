<?php
/**
 * @section Filename
 * setupPageAjax.php
 * @subsection Description
 * Administration ajax request handler that serves the responses to the Ajax requests that synchronizes and test the connection between PM and the Pentaho Bi server.
 * @param string $_POST['action'] the action to be executed.
 * @author Colosa Development Team 2010
 * @date 17/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */

  /**
   * The action to be evaluated
   */
  $action = $_POST['action'];

  require_once ( "class.pentahoreports.php" );
  
  /**
   * The main pentahoreports object.
   */
  $objPentaho = new pentahoreportsClass();

  /**
   * reading the pentaho local configuration.
   */
  $objPentaho->readLocalConfig( SYS_SYS );
  
  switch ( $action ) {
    // creates the tables based in the sql queries
    case 'database' :
      try {
        $res = $objPentaho->setupDatabase();
        // setup the database
        if ( $res == 1 ) 
          print G::LoadTranslation("PENTAHO_LABEL_TABLE_SUCCESS");//"Table APP_CACHE_VIEW and triggers are installed successfully";
        else
          throw ( new Exception ( $res ) );
      }
      catch (Exception $e ) {
        print "<font color='red'>" . $e->getMessage() . "</font>";
      }
      break;
    // creates the workspace repository in the pentaho solution if not exists
    case 'workspace' :
      try {
      	
        // calling the method
        $result = $objPentaho->getSolutionRepository();
        $fields = $objPentaho->readLocalConfig ( SYS_SYS );
        if ( isset( $fields['PentahoUsername'] ) && $fields['PentahoUsername'] != '' )  {
          $workspace = substr($fields['PentahoUsername'],3);
        }
        else
          $workspace = SYS_SYS;
        
        // if the result is an array then if there are files in the solution repository then do nothing
        
        $exists = false;
        if ( is_array($result) )  {
          foreach ( $result as $key => $res ) {
            if ( $res['name'] == $workspace && isset( $res['files'] ) && count ( $res['files'] ) > 10 ) $exists = true;
          }
        }

        //before if ( is_array($result) && $result[0]['name'] == $workspace && isset( $result[0]['files'] ) && count ( $result[0]['files'] ) > 10 ) {
        if ( $exists ) {
          print G::LoadTranslation("PENTAHO_LABEL_TABLE_ALREADY_SYNCH") . "($workspace)";//"workspace already synchronized with Pentaho Solution";
        } 
        else {
          // else create the workspace solution
          $result = $objPentaho->createNewSolutionWorkspace( SYS_SYS  );
          if ( $result == true ) 
            print G::LoadTranslation("PENTAHO_LABEL_TABLE_SYNCHED") . "(" . SYS_SYS .")";//"workspace synchronized with Pentaho Solution";
          else
            throw ( new Exception ( $res ) );
        }
      }
      catch (Exception $e ) {
        print "<font color='red'>" . $e->getMessage() . "</font>";
      }

      break;
    // re-recreate the workspace repository even if that exists in the pentaho server
    case 'rebuild' :
      try {
        // calling the methods
        $result = $objPentaho->getSolutionRepository();
        $result = $objPentaho->createNewSolutionWorkspace( SYS_SYS );
        $objPentaho->synchronizeReportTables();
        // evaluating the result the adequate message is showed
        if ( $result == true )
          print G::LoadTranslation("PENTAHO_LABEL_WS_SYNCHED");//"workspace synchronized with Pentaho Solution";
        else
          throw ( new Exception ( $res ) );
      }
      catch (Exception $e ) {
        print "<font color='red'>" . $e->getMessage() . "</font>";
      }

      break;
    // create the pentaho user for the current workspace
    case 'pentahoUser' :
      try {
        // calling the method
        $res = $objPentaho->testPentahoUser();
      }
      // evaluating the result the adequate message is showed
      catch (Exception $e ) {
        print "<font color='red'>" . $e->getMessage() . "</font>";
      }
      break;
    // create the jndi data source for the current pentaho connection
    case 'jndi' :
      try {
        $res = $objPentaho->createJndi ();
      }
      // evaluating the result the adequate message is showed
      catch (Exception $e ) {
        print "<font color='red'>" . $e->getMessage() . "</font>";
      }
      break;
  }
