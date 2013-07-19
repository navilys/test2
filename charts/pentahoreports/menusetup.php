<?php

/**
 * @section Filename 
 * menuSetup.php
 * @subsection Description
 * Adding the pentaho item in the setup/administration menu
 * @author Gustavo Cruz <gustavo@colosa.com>
 * @subsection Copyright
 * Copyright (C) Colosa Development Team 2010
 * <hr>
 * @package plugins.pentahoreports.menues
 */

  global $G_TMP_MENU;
  $G_TMP_MENU->AddIdRawOption('PENTAHO', '../setup/pluginsSetup?id=pentahoreports.php', "Pentaho Reports Admin" , '../plugin/pentahoreports/pentaho.png', '', 'admToolsContent'  );  
  
