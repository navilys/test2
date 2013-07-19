<?php
/**
 * @section Filename
 * reports_load.php
 * @subsection Description
 * Loading the reports content inside processmaker
 * @author gustavo cruz <gustavo@colosa.com>
 * @date 17/05/2010
 * @subsection Copyright
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 * <hr>
 * @package plugins.pentahoreports.scripts
 */
?>
﻿<style>
body {
 overflow:hidden;
}

#loadPage{
  position: absolute;
  top: 200px;
  left: 200px;
}

.overlay{
  display: block;
  position: absolute;
  top: 87;
  left: 0;
  width: 101%;
  height: 100%;
  background: #ECECEC;
  z-index:1001;   
  padding: 0px;
}

.modal {
  display: block;
  position: absolute;
  top: 25%;
  left: 42%;
  background: #000;
  padding: 0px;
  z-index:1002;
  overflow: hidden;
  border: solid 1px #808080;
  border-width: 1px 0px;
}

.progress {
    display: block;
    position: absolute;
    padding: 2px 3px;
}

.container
{
  
}
.header
{
  background: url(/images/onmouseSilver.jpg) #ECECEC repeat-x 0px 0px;
  border-color: #808080 #808080 #ccc;
  border-style: solid;
  border-width: 0px 1px 1px;
  padding: 0px 10px;
  color: #000000;
  font-size: 9pt;
  font-weight: bold;
  line-height: 1.9;
  font-family: arial,helvetica,clean,sans-serif;
} 

.body
{
  background-color: #f2f2f2;
  border-color: #808080;
  border-style: solid;
  border-width: 0px 1px;
  padding: 10px;
}
</style>

<div id="fade" class="overlay"></div>
<div class="modal" id="light">
  <div class="header"><?=G::LoadTranslation('ID_LOADING')?></div>
  <div class="body">
    <img src="/images/activity.gif" />
  </div>
</div>
<iframe name="reportsFrame" id="reportsFrame" src ="pentahoReportsListContent" width="99%" height="800" frameborder="0">
  <p>Your browser does not support iframes.</p>
</iframe>

<script>
    oReportsFrame  = document.getElementById('reportsFrame');
    oClientWinSize = getClientWindowSize();
    oReportsFrame.style.height = oClientWinSize.height-100;
    
    document.getElementById('fade').style.height = oClientWinSize.height;
    document.documentElement.style.overflowY = 'hidden';
</script>

