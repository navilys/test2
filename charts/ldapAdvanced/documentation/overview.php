<?php

/**
  @mainpage Overview
  @dontinclude overview.php
  @section Introduction
  The Ldap Advanced Plugin is an extension that can be installed in a ProcessMaker  server
  in order to bring ability to use LDAP/Active Directory accounts like normal ProcessMaker accounts.

  This Advanced LDAP plugin works for Open Ldap and Microsoft Active Directory and  it  is
  using their accounts credentials to login into ProcessMaker. Also this plugins allows you to
  maintain synchronized your server's account list with the ProcessMaker account list.

  Recent new created accounts will be created automatically in ProcessMaker.

  For employees/accounts resigned from the company, but still accounts in LDAP, you need to move them to "Terminated" OU and
  ProcessMaker will consider them like disabled accounts.

  Current version of this plugin also synchronize groups.

  For accounts created using this plugin, the authentication is done in the Server, and
  ProcessMaker dont save the real password, just verify the password using the server.


  @subpage PAGE_FEATURES
  - @ref SEC_LDAP_LOGIN
  - @ref SEC_IMPORT_USERS
  - @ref SEC_AUTOREGISTER
  - @ref SEC_DISABLED_ACCOUNTS
  - @ref SEC_LOG


  @subpage PAGE_REQUERIMENTS
  - @ref SEC_SERVER
  - @ref SEC_AL_PLUGIN

  @subpage PAGE_HOW_WORKS
  - @ref SEC_HOW_PLUGIN
  - @ref SEC_HOW_CONNECTS
  - @ref SEC_HOW_AUTOREGISTER
  - @ref SEC_HOW_SYNCHRONIZE
  - @ref SEC_MAIN_CLASSES
  - @ref SEC_SETUP_DEV

  @subpage PAGE_GLOSARY

  @section SEC_LINKS Documentation in ProcessMaker web sites:
  - <a href='http://http://wiki.processmaker.com/index.php/ProcessMaker_-_AdvancedLDAP'>ProcessMaker Wiki - ldapAdvanced</a> \n
  - <a href='http://http://http://processmaker.com/advanced-ldap'>ProcessMaker Videos - ldapAdvanced</a>
*/

/**
  @page PAGE_FEATURES Main Features
  @section SEC_LDAP_LOGIN Open Ldap and Active Directory Authentication
    ProcessMaker has a plugin-able RBAC authentication design.

    This design allows you to define you own ways to Verify authentication for users.

    This plugin is an example of how extent RBAC to allow users to be authenticated with Open Ldap and Active Directory

    You need to define a connection, this connection is called Authentication Source.

    An Authentication Source, basically is the server and the port of Active Directory or Open Ldap server.

    Also defines if the connection will use anonymous or user based connection.

    PM creates a record for the user in the database with a flag that this user should be authenticated against Active Directory/LDAP.

    @subsection SEC_SETUP_AUTHSOURCE Setup an Authentication Source
    The option to setup the Authentication Sources is on the User tab menu, inside the Admin Main Menu.

    This panel shows on the top a link to create a new authentication source, and on the bottom an authentication sources list.

    In order to create a New Authentication Source, Click on New to create an authentication source. \n
    Then select the provider:

    - Provider: This drop-down box field will have all the options available for the server \n
    a provider is any RBAC plugin installed in server, the default providers are the SQL based \n
    ProcessMaker authentication, and the simple Ldap authentication.  \n
    if this plugin (AdvancedLdap) is correctly installed, will appear in this dropdown.

    After that you need Define the fields in the authentication source form. \n
    - The server needs to be a valid Active Directory or Open Ldap server.
    - Usually nobody uses an anonymous connection, and you need to configure a valid user and password to connect.
    - the base DN is the base from all the searchs will be done.
    - the object field is a multiline textarea, this fields is used to search users, the default is * (asterisk)
      and means all classes.  Remember an Active Directory or LDAP is a list of nodes, and every node has one
      or more classes.  The default * should work, but in case the query returns many rows, is better constraint the
      query for users only.  In Active Directoy this means the class Person, for Open Ldap the class is user.
    - the filter fields, is another multiline textarea, to collect information about which fields will be used to search
      users, in Active Directory usually the user id is in the sAMAccountName, UserPrincipalName, cn or dn.
      In Open Ldap is in uid, cn, dn

    - The dropdown to define is the connection is to Active Directory or Open Ldap, is used internally to use different
      fields when map the email, first name, last name and other fields.

    - the User Identifier Field is used to check if an account was previouly imported or not.
    - the OU Terminated is used for the fired/disabled OU, that means all accounts moved to this OU

    @image html ldapAdvancedForm.png "ldapAdvanced Form with an Active Directory Setup "

    @subsection SEC_AUTHSOURCE_LIST Authentication Source List

    In this list there is the name, provider, server name and port for every one of the authentication sources. \n
    Also there are three link options: \n

    - Edit: This link field takes the user to the same panel for the creation of an authentication source, with the filled fields.
    - Delete: This link field deletes the authentication source.
    - Import Users: This link field imports the users from the created authentication source.
    - Synchronize Departments: This link show the tree of the OU in the Open LDAP/Active Directory.
    - Synchronize Groups: This link show the list of the groups in the Open LDAP/Active Directory.


  @section SEC_IMPORT_USERS Import Users
    To import click on the Import Users link.

    On the panel Search you have to introduce a Keyword: This field is used to make queries to database and retrieve
    as many users matches the keyword.  The search is a *keyword* pattern. \n

    Click on search to see a list that matches the keyword typed. The list has the following fields

    - [SELECT-ALL]:This check box field is to select the user. If the user has already been imported a text User name already exists:(name), will show instead of the check box. Press on [SELECT-ALL] so all the users can be checked at once.
    - Name: This field shows the user's complete name.
    - E-Mail: This field shows the user's email
    - Distinguished Name: This field shows the users DN. The DN is a chain of information needed to validate a user, such as the user name, domain, etc.
    - Import: This button is to import the checked users.

    AdvancedLdap plugin uses the User Identifier Field to check if an account was previouly imported or not.

  @section SEC_SYNC_DEPARTMENTS Synchronize Departments
    In the tree will display all existing departments in the "Active Directory" server in a hierarchy way, each department has a checkbox on the right side, which allows it to be selected or deselected for consideration by the cron for the synchronization.

    For departments that were selected and ran the cron will display the number of users that belong to them, which were successfully imported to PM.

    Once selected and / or deselect the departments, to save the changes it must be pressed button "Save Changes", which is located at the bottom right.

    @image html syncDepartaments.png "Synchronize Departments tree panel"

  @section SEC_SYNC_GROUPS Synchronize Groups
    In the tree will display all existing groups in the "Active Directory" server in a hierarchy way, each group has a checkbox on the right side, which allows it to be selected or deselected for consideration by the cron for the synchronization.

    For groups that were selected and ran the cron will display the number of users that belong to them, which were successfully imported to PM.

    Once selected and / or deselect the groups, to save the changes it must be pressed button "Save Changes", which is located at the bottom right.

    @image html syncGroups.png "Synchronize Groups tree panel"

  @section SEC_AUTOREGISTER Automatic Registers of users
    This feature and the Synchronization are very useful because the ProcessMaker administrator don't need to create one by one every account in ProcessMaker.
    meanwhile the Synchronization create and syncronize users in specific departments, the automatic register will create an account in ProcessMaker
    for users still not created in ProcessMaker but already created in Active Directory.

    The new created user should go to ProcessMaker login page and then provide their Active Directory/OpenLdap password
    and the plugin after check the password will create a new account in ProcessMaker automatically.

    The user is created with the PM_OPERATOR Role.

    this feature can be enabled or disabled in the Authentication Source Form.

    Probably if the automatic Synchronization is enabled, this feature should be disabled.

  @section SEC_DISABLED_ACCOUNTS Disabled or Fired accounts
    For employees/accounts resigned from the company, but still accounts in LDAP, this plugin allows you
    define an OU for these ex-valid users.  This OU is for to move them to the Terminated OU.

    ProcessMaker will consider them like disabled accounts.

    The syncronize process will check for every user his current OU, if the OU for any user is the same
    as the OU specified in the Terminated OU, that user will be disabled.

  @section SEC_LOG Log for Monitor ldap activities
    New version of the plugin creates automatically a text log for all activities.

    This log is very useful for debug purposes, or just to see what is happening with the plugin.

    This log is located in  shared/log/ldapAdvanced.log

    there is only one log file for all workspaces.

    the methods logged are
    - bind to server like anoymous or with user credentials
    - sucessful logins for accounts
    - unsucessful logins
    - filter used in searchs
    - how many users are returned in each search
    - accounts automatic registered
    - OUs syncronized

    also logs the ldap error, and the error message in case there are an error in the connection.

    @image html ldapAdvancedLog.png "Log example in /shared/log/ldapAdvanced.log"

*/

/**
  @page PAGE_REQUERIMENTS Requeriments
  @section SEC_SERVER Access to a Open LDAP or Active Directory server

    - Basically you need an Active Directory or Open Ldap  server running.

    - Credentials for a valid account in the LDAP server, with permissions to execute searchs.

    - DNS Domain entries, in case your network need it.

    - It is useful if you have access to the server to check locally any query sent for the plugin to the server

    - the php_ldap extension


  @section SEC_AL_PLUGIN The ProcessMaker - AdvancedLDAP plugin.
    - Install and configure an authentication source using the advancedLdap plugin

*/

/**
  @page PAGE_HOW_WORKS How the plugin works
  @section SEC_HOW_PLUGIN Plugin Architecture
    Plugins allow additional functionality and interface customization to be added to ProcessMaker.

    The main aim for plug-ins is to add new features to the ProcessMaker

    @subsection SUB_CLASSES Plugin and PluginRegistry classes
    A plugin in ProcessMaker is basically a class descendent of class Plugin and PluginRegistry in ProcessMaker core.

    There are two classes in the Core to allow plugins to work, the plugin and pluginRegistry class:

    Here a graph to describe the main methods and the relationship between a plugin and the core classes:

    @image html pluginClasses.png "Plugin and PluginRegistry Classes"

    @subsection SUB_PLUGINFOLDERS Plugin Folders
    The files in a plugin are classified in some specific folder, meaning of all folder is described below

    Next image show the folder structure for a plugin

    @image html pluginFolder.png "folder structure"

    @subsection SUB_OWNPLUGINS Creating your own Plugins
    These are the steps to creating the small and simplest pugin using the Gulliver framework.

    1. Enter the directory /<processmaker home>/workflow/engine

    2. Execute ./gulliver new-plugin <name_of_plugin>

       In this step the wizard will create the directories and files needed to run a basic application.
       In this tutorial, we'll create the plugin demo that will change the logo of ProcessMaker.

    3. Enable the plugin in your Workspace

    4. Now we're ready to test.


    @subsection SUB_ADVANCEDPLUGINS Creating  Advanced Plugins
    These are the steps to creating the small and simplest plugin using the Gulliver framework.

    1. Example page and adding options to main menu

    2. Creating Roles, Permissions and Redirect

    3. External Step

    4. Pm Functions

    5. using propel Tables

    @subsection SUB_DEVCYCLE Plugin Development Cycle.

    @image html pluginCycle.png "Develop Cycle for a plugin"



  @section SEC_HOW_CONNECTS How the class connect to server
    The plugin uses the php_ldap extension

  @section SEC_HOW_AUTOREGISTER The autoregister feature
    this works using the method ...

  @section SEC_HOW_SYNCHRONIZE How the synchronize feature works
    manual setup

    automatic synchronization using the ProcessMaker Cron.

  @section SEC_MAIN_CLASSES Main Classes
    main classes are

  @section SEC_SETUP_DEV Setup a Development workspace
    In order to set up a Development Environment with the Pentaho Connector Plugin is necessary
    remember to work in a separate workspace in order to avoid conflicts between you and other developers,
    because not all of your coworkers have this plugin installed in theirs working sites. \n

  @subsection The Setting Up
    Another good development practice can be separate the plugins repository from the main
    ProcessMaker environment, to accomplish this, a couple of symbolic links can be added in
    the main plugin folder, those are linked to the external plugin folder, for example:\n
    <code>
    ln -s /<plugins_path>/pentahoreports/pentahoreports/ /<processmaker_path>/workflow/engine/plugins/\n
    ln -s /<plugins_path>/pentahoreports/pentahoreports.php /<processmaker_path>/workflow/engine/plugins/\n
    </code>

*/

/**
  @page PAGE_GLOSARY Glosary
  @section G1 Active Directory
    is the primary Microsoft LDAP directory service product and a central component of the Windows platform. \n
    Active Directory directory service is the distributed directory service of the Microsoft Windows Server 2003 and Microsoft Windows 2000 Server operating systems. Active Directory enables centralized, secure management of an entire network. \n
    Active Directory provides the facilities to manage identities and relationships comprising network environments.

    Active Directory provides a way to manage network resources, including printers, e-mail, and user accounts. \n
    Active Directory treats network resources as objects that are arranged in a hierarchical framework called a forest.

  @section G2 Active Directory Domain
    Domains are groups of computers and other network resources connected to a central directory service (e.g., Austin Active Directory). \n
    Along with forests and trees, domains are a primary component of the typical Active Directory network structure.

  @section G3 Computer Object
    A computer object refers to a computer that is connected in the Active Directory forest.


  @section G4 Directory Service Command Line utility (dscl)
    The Directory Service Command Line utility is an application provided by Apple with Mac OS X that is used for creating, reading, and managing directory service data, including data managed by Active Directory. For more information regarding how to use dscl, see Apple's dscl developer documentation.

  @section G41  Distinguished names (DNs)
    Every entry in the directory has a distinguished name (DN). \n
    The DN is the name that uniquely identifies an entry in the directory. \n
    A DN is made up of attribute=value pairs, separated by commas, for example: \n
      cn=Ben Gray,ou=editing,o=New York Times,c=US \n
      cn=Lucille White,ou=editing,o=New York Times,c=US \n
      cn=Tom Brown,ou=reporting,o=New York Times,c=US \n

    Any of the attributes defined in the directory schema may be used to make up a DN. \n
    The order of the component attribute value pairs is important. \n
    The DN contains one component for each level of the directory hierarchy from the root down to the level where the entry resides. LDAP DNs begin with the most specific attribute (usually some sort of name), and continue with progressively broader attributes, often ending with a country attribute. The first component of the DN is referred to as the Relative Distinguished Name (RDN). It identifies an entry distinctly from any other entries that have the same parent. In the examples above, the RDN "cn=Ben Gray" separates the first entry from the second entry, (with RDN "cn=Lucille White"). These two example DNs are otherwise equivalent. The attribute=value pair making up the RDN for an entry must also be present in the entry. (This is not true of the other components of the DN.)


  @section G5 Domain
    See also: Active Directory Domain

  @section G6 Forest
    The forest refers to the collection of all objects managed by an Active Directory network. Forests can contain objects, object attributes, and rules. Along with trees and domains, the forest is a primary component of the typical Active Directory network structure. See also: Domain, Objects, Tree

  @section G7 Group
    Groups are types of objects that can contain computers, users, or other groups. See also: Objects

  @section G8 Group Policy Object (GPO)
    Group policy objects contain rules that are applied to organizational units. See also: Organizational Unit (OU)

  @section G81 LDAP Attributes

    - CN - Common Name.	Actually, this LDAP attribute is made up from givenName joined to SN.
    - description 	What you see in Active Directory Users and Computers.  Not to be confused with displayName on the Users property sheet.
    - displayName 	If you script this property, be sure you understand which field you are configuring.  DisplayName can be confused with CN or description.
    - physicalDeliveryOfficeName Office's LDAP attribute:
    - E-mail is plain: mail
    - DN - also distinguishedName 	DN is simply the most important LDAP attribute.
    - givenName 	Firstname also called Christian name
    - homeDrive 	Home Folder : connect.  Tricky to configure
    - name 	Exactly the same as CN.
    - objectCategory 	Defines the Active Directory Schema category. For example, objectCategory = Person
    - objectClass 	Also used for Computer, organizationalUnit, even container.  Important top level container.
    - physicalDeliveryOfficeName 	Office! on the user's General property sheet
    - sAMAccountName 	sAMAccountName = guyt.  Old NT 4.0 logon name, must be unique in the domain.  Can be confused with CN.
    - SN This would be referred to as last name or surname.
    - userAccountControl 	Used to disable an account.  A value of 514 disables the account, while 512 makes the account ready for logon.
    - userPrincipalName  Often abbreviated to UPN, and looks like an email address.  Very useful for logging on especially in a large Forest.   Note UPN must be unique in the forest.

  @section G9 Objects
    Network resources managed by an Active Directory server are referred to as objects. \n
    Objects can be computers, printers, or other peripherals connected to Active Directory. \n
    Each object is identified by a unique name. See also: Forest

  @section G10 Organizational Unit (OU)
    Organizational units are logical groups of objects in the Active Directory forest. \n
    In the case of Austin Active Directory, organizational units are used to group all of the objects in a particular department. \n
    See also: Forest, Objects

  @section G11 Tree
    Trees are trust-based groupings of related domains. \n
    Along with forests and domains, trees are a primary component of the typical Active Directory network structure. \n
    See also: Domain, Forest

  @section G12 Trust
    Trusts describe the ability of users to access resources in domains they do not belong to. Transitive trust, or trust that exists between all domains in a logical grouping (i.e., a tree), is created automatically between domains within a single forest. For more information on trusts, see Microsoft's documentation on managing trusts.

*/


