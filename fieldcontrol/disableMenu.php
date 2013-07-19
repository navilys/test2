<?php
  G::LoadClass('pmFunctions');
  require_once ("classes/model/Users.php");
  global $G_TMP_MENU;
  global $RBAC;  
  $users=$_SESSION['USER_LOGGED'];

  $G_TMP_MENU->DisableOptionId("GROUPS");
  $G_TMP_MENU->DisableOptionId("ROLES");
  $G_TMP_MENU->DisableOptionId("USERS");
  $G_TMP_MENU->AddIdRawOption('MT_GROUPS_ROLES', '../fieldcontrol/groupsRoles', "Permissions","","","users" );
  $G_TMP_MENU->AddIdRawOption('USERS', '../fieldcontrol/users_List', "Users","","","users" );
  $G_TMP_MENU->AddIdRawOption('INBOX', '../fieldcontrol/admininbox', "Admin Inbox","","","users" );
  $G_TMP_MENU->AddIdRawOption('ACTION_INBOX', '../fieldcontrol/actions_Inbox', "Actions Inbox","","","users" );
  $G_TMP_MENU->AddIdRawOption('MY_PROFILE', '../fieldcontrol/my_profile', "My Profile","","","users" );