<?php
/**
 * @section Filename
 * class.ProxyPentahoUser.php
 * @subsection Description
 * this class encapsulates all the essential methods in order to connect
 * get a report list for each workspace, and generate these reports for each workspace
 * also manages the connection to the pentaho reports and the plugin administration area.
 * @author Fernando Ontiveros
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.proxy
 */
 
  class PentahoProxy  {
    /**
     * This is the default separator for the assembling of the rpc requests to the prentaho server;
     */
    const RPC_SEPARATOR_CHAR = "|";
    /**
     * this is the pentaho proxy constructor method
     * that defines the RPC_SEPARATOR_CHAR constant
     */
    function __construct (  ) {
      if ( true /* version = 3.5.2 */ ) 
        define ( 'RPC_SEPARATOR_CHAR', "|" );
      else
        define ( 'RPC_SEPARATOR_CHAR', "\xef" . "\xbf" ."\xbf" );
    }
  }    
