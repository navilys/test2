<?php
/**
 * @section Filename
 * class.ProxyPentahoUser.php
 * @subsection Description
 * this class encapsulates all the essential methods in order to connect
 * get a report list for each workspace, and generate these reports for each workspace
 * also manages the connection to the pentaho reports an the plugin administration area.
 * @author Fernando Ontiveros
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.classes.proxy
 */
 
  require_once ( 'class.PentahoProxy.php' );
  require_once ( PATH_PLUGINS . 'pentahoreports/class.pentahoreports.php' );

  class ProxyPentahoUser extends PentahoProxy  {
    
    /**
     *  String pentaho administrator user uid
     */
    var $userId;
    /**
     *  String password for the administrator user 
     */
    var $password;
    /**
     *  String description for the user
     */
    var $description;
    /**
     *  String workspace for the user
     */
    var $workspace;
    /**
     *  Object pentahoreports Object 
     */    
    var $objPentaho;
    /**
     *  String pentaho server uri
     */
    var $sPentahoServer;
    /**
     *  String pentaho administration console uri
     */
    var $sPentahoAdmServer;

    /**
     * this is the pentaho proxy constructor method
     * also initializes some of the object attributes.
     */
    function __construct (  ) {
      $this->objPentaho = new pentahoreportsClass();
      $this->objPentaho->readConfig();
      $this->sPentahoServer    = $this->objPentaho->sPentahoServer;
      $this->sPentahoAdmServer = $this->objPentaho->sPentahoAdmServer;    	
    }

    /**
     * this method creates a pentaho user in the Pentaho bi server.
     * @author Fernando Ontiveros
     * @param String $userId user id
     * @param String $password user password
     * @param String $description Description
     * @return Mixed server response
     */
    function createUser ( $userId, $password, $description ) {
    	$this->userId      = $userId;
    	$this->password    = $password;
    	$this->description = $description;
      // assembling the request
      $payload = $this->serializeCreateUser();     	
      // the pentahoreports object sends the request
      $response = $this->objPentaho->getPAC( $payload );      
      if ( $response['status'] == 'OK' ) {
      }
      return $response;      
    }

    /**
     * This method deletes a pentaho user in the Pentaho bi server.
     * @author Fernando Ontiveros
     * @param String $userId user id
     * @param String $description Description
     * @return Mixed server response
     */
    function deleteUser ( $userId, $description ) {
      $this->userId      = $userId;
      $this->description = $description;
      // assembling the request
      $payload = $this->serializeDeleteUser();
      // the pentahoreports object sends the request
      $response = $this->objPentaho->getPAC( $payload );
      return $response;      
    }    

    /**
     * This method sets the roles for a pentaho user in the Pentaho bi server.
     * @author Fernando Ontiveros
     * @param String $userId user id
     * @param String $description Description
     * @return Mixed server response
     */
    function setRoles ( $userId, $description ) {
      $this->userId      = $userId;
      $this->description = $description;
      // assembling the request
      $payload = $this->serializeSetRoles();     	
      // the pentahoreports object sends the request
      $response = $this->objPentaho->getPAC( $payload );
      return $response;      
    }

    /**
     * This method creates the datasource for a workspace in the Pentaho bi server.
     * @author Fernando Ontiveros
     * @param String $dsName     database name
     * @param String $dsUrl      database url
     * @param String $dsUserName database user
     * @param String $dsPassword database password
     * @return Mixed server response
     */
    function createDatasource ( $dsName, $dsUrl, $dsUserName, $dsPassword ) {
    	$this->dsName      = $dsName;
    	$this->dsUrl       = $dsUrl;
    	$this->dsUserName  = $dsUserName;
    	$this->dsPassword  = $dsPassword;
      // serializing the data ina a variable
      $payload = $this->serializecreateDatasource();     	
      // the pentahoreports object executes the request
      $response = $this->objPentaho->getPAC( $payload );
      return $response;
    }

    /**
     * This method gets the pentaho solution information data.
     * @author Fernando Ontiveros
     * @param String $workspace workspace
     * @param String $userId    user
     * @return Mixed server response
     */
    function setSolutionInfo ( $workspace, $userId ) {
    	$this->userId      = $userId;
    	$this->workspace   = $workspace;

      $payload = $this->serializesetSolutionInfo();     	
      
      $response = $this->objPentaho->getMantle( $payload );      
      return $response;      
    }

    /**
     * This method gets the pentaho solution user list.
     * @author Fernando Ontiveros
     * @return Array The pentaho user list
     */
    function getUsers ( ) {
      $payload = $this->serializegetUsers();     	
      
      $response = $this->objPentaho->getPAC( $payload );      
      if ( $response['status'] == 'OK' ) {
        // in the response there are an array with the user description and the user, we need to walk thru this array 
        // and build the array user, the array start with the users.ProxyPentahoUser, when we find this text in the $val
        // the list of users follows in the next items.

        $keyForFirstUser = 0;
      	// get the first key for the first user
      	foreach ( $response['tokens'] as $key => $val ) 
      		if ( strpos ( $val, "org.pentaho.pac.common.users.ProxyPentahoUser" ) !== false ) {
      			$keyForFirstUser = $key + 2;  // plus two, because the next items come in blank.
      		}
        // then iterates the user list 
      	for ( $i = $keyForFirstUser; $i < count($response['tokens']); $i++ ) {
          $keyValue = $response['tokens'][ $i ];
          if ( substr($keyValue,0,1) == '"' ) $keyValue = substr($keyValue,1 );
          if ( substr($keyValue,-1) == '"' )  $keyValue = substr($keyValue,0, strlen($keyValue) -1 );
          $response['tokens'][ $i ] = $keyValue;
        }
        
        // if there are no users
        if ( $keyForFirstUser == 0 ) {
          throw ( new Exception ( "seems the Pentaho Server does not have a valid user list" ) );
        }
        $users = array ();
        // if there are users, proceed to assemble the response
      	for ( $i = $keyForFirstUser; $i < count($response['tokens']); $i++ ) {
      	  if ( isset($response['tokens'][ $i +1 ]) && isset ($response['tokens'][ $i ]) ) {
            $keyValue = $response['tokens'][ $i + 1 ];
            $valValue = $response['tokens'][ $i];
            $users[ $response['tokens'][ $i +1 ] ] = $response['tokens'][ $i];
          }
          $i++;
      	}
      		
      	return $users;
      }
      
    }
    
    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to create a user.
     * @author Fernando Ontiveros
     * @return String
     */
    function serializeCreateUser () {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoAdmServer . '/' . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "C4BBC1ED4AB5244A8629FFB058A47F11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.client.PacService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "createUser" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.users.ProxyPentahoUser" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.users.ProxyPentahoUser/521529907" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->description . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->userId . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->password . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "6" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "7" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }
    
    //5|0|10|http://...:8099/|C4BBC1ED4AB5244A8629FFB058A47F11|
    //org.pentaho.pac.client.PacService|deleteUsers|
    //[Lorg.pentaho.pac.common.users.ProxyPentahoUser;|
    //[Lorg.pentaho.pac.common.users.ProxyPentahoUser;/60356795|
    //org.pentaho.pac.common.users.ProxyPentahoUser/521529907|
    //juanito 6|juan6||1|2|3|4|1|5|6|1|7|8|1|9|10|

    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to delete a user.
     * @author Fernando Ontiveros
     * @return String
     */
    function serializeDeleteUser () {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoAdmServer . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "C4BBC1ED4AB5244A8629FFB058A47F11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.client.PacService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "deleteUsers" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "[Lorg.pentaho.pac.common.users.ProxyPentahoUser;" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "[Lorg.pentaho.pac.common.users.ProxyPentahoUser;/60356795" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.users.ProxyPentahoUser/521529907" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->description . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->userId . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "6" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "7" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }
    
    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to get the roles list
     * @author Fernando Ontiveros
     * @return String
     */
    function serializeSetRoles () {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "14" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoAdmServer . '/' . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "C4BBC1ED4AB5244A8629FFB058A47F11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.client.PacService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "setRoles" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.users.ProxyPentahoUser" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "[Lorg.pentaho.pac.common.roles.ProxyPentahoRole;" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.users.ProxyPentahoUser/521529907" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->userId . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "[Lorg.pentaho.pac.common.roles.ProxyPentahoRole;/2321381908" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.roles.ProxyPentahoRole/524429538" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "User has logged in" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "Authenticated" . PentahoProxy::RPC_SEPARATOR_CHAR;
//1|2|3|4|2|5|6|7|8|1|9|10|11|1|12|13|14|          
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "6" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "7" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "12" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "13" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "14" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }

//5|0|12|http://,,,/|C4BBC1ED4AB5244A8629FFB058A47F11|org.pentaho.pac.client.PacService|createDataSource|org.pentaho.pac.common.datasources.PentahoDataSource|org.pentaho.pac.common.datasources.PentahoDataSource/484838215|com.mysql.jdbc.Driver|x1|atopml2005||jdbc:mysql://rodimus.colosa.net:3306/wf_pentaho|root|1|2|3|4|1|5|6|7|0|0|8|9|10|11|12|0|0|
    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to create the data source
     * @author Fernando Ontiveros
     * @return String
     */
    function serializecreateDataSource() {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "12" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoAdmServer . "/" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "C4BBC1ED4AB5244A8629FFB058A47F11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.client.PacService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "createDataSource" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.datasources.PentahoDataSource" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.common.datasources.PentahoDataSource/484838215" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "com.mysql.jdbc.Driver" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->dsName . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->dsPassword . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->dsUrl . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->dsUserName . PentahoProxy::RPC_SEPARATOR_CHAR;
//1|2|3|4|1|5|6|7|0|0|8|9|10|11|12|0|0|
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "6" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "7" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "12" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }
    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to get the users list
     * @return String
     */

    function serializegetUsers() {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoAdmServer . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "C4BBC1ED4AB5244A8629FFB058A47F11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.pac.client.PacService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "getUserRoleSecurityInfo" . PentahoProxy::RPC_SEPARATOR_CHAR;
//|1|2|3|4|0|
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }


//5|0|15|http://..:8080/pentaho/mantle/|21E0CCC4D57AE1804330A709F2D93448|org.pentaho.mantle.client.service.MantleService|setSolutionFileInfo|org.pentaho.mantle.client.objects.SolutionFileInfo|org.pentaho.mantle.client.objects.SolutionFileInfo/2613194657|java.util.Date/1659716317|pentaho||java.util.ArrayList/3821976829|org.pentaho.mantle.client.objects.RolePermission/3117205153|Admin|org.pentaho.mantle.client.objects.SolutionFileInfo$Type/907648790|org.pentaho.mantle.client.objects.UserPermission/2494938341|wf_pentaho|
    /**
     * This assembles the connection tokken that is sent to the pentaho server in order to set the solution information
     * @author Fernando Ontiveros
     * @return String
     */

    function serializesetSolutionInfo() {
      $result = '';
      $result = "5" . PentahoProxy::RPC_SEPARATOR_CHAR;   // version 5 is required for pentaho 3.5.2
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;  // ?
      $result .= "15" . PentahoProxy::RPC_SEPARATOR_CHAR;  // tokens in the rpc request
      $result .= $this->sPentahoServer . "/mantle/" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "21E0CCC4D57AE1804330A709F2D93448" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.mantle.client.service.MantleService" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "setSolutionFileInfo" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.mantle.client.objects.SolutionFileInfo" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.mantle.client.objects.SolutionFileInfo/2613194657" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "java.util.Date/1659716317" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->workspace . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "java.util.ArrayList/3821976829" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.mantle.client.objects.RolePermission/3117205153" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "Admin" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= 'org.pentaho.mantle.client.objects.SolutionFileInfo$Type/907648790' . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "org.pentaho.mantle.client.objects.UserPermission/2494938341" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= $this->userId . PentahoProxy::RPC_SEPARATOR_CHAR;
//1|2|3|4|1|5|6|1|0|1|0|7|2196762384|1271310319616|
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "3" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "4" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "6" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "7" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "2196762384" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1271310319616" . PentahoProxy::RPC_SEPARATOR_CHAR;
//8|9|9|0|10|1|11|-1|12|0|0|8|1|13|5|10|1|14|-1|15|      
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "9" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "11" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "-1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "12" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "0" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "8" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "13" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "5" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "10" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "14" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "-1" . PentahoProxy::RPC_SEPARATOR_CHAR;
      $result .= "15" . PentahoProxy::RPC_SEPARATOR_CHAR;
      
      return $result;
    }
 
  }
  
  
/*  
  
test datasource
5|0|12|http://...:8099/|C4BBC1ED4AB5244A8629FFB058A47F11|org.pentaho.pac.client.PacService|testDataSourceConnection|org.pentaho.pac.common.datasources.PentahoDataSource|org.pentaho.pac.common.datasources.PentahoDataSource/484838215|com.mysql.jdbc.Driver|x1|atopml2005||jdbc:mysql://rodimus.colosa.net:3306/wf_pentaho|root|1|2|3|4|1|5|6|7|0|0|8|9|10|11|12|0|0|

create datasource
5|0|12|http://,,,/|C4BBC1ED4AB5244A8629FFB058A47F11|org.pentaho.pac.client.PacService|createDataSource|org.pentaho.pac.common.datasources.PentahoDataSource|org.pentaho.pac.common.datasources.PentahoDataSource/484838215|com.mysql.jdbc.Driver|x1|atopml2005||jdbc:mysql://rodimus.colosa.net:3306/wf_pentaho|root|1|2|3|4|1|5|6|7|0|0|8|9|10|11|12|0|0|

*/
