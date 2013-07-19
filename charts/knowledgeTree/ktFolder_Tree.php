<?php
$html = '';
if (substr(SYS_SKIN, 0, 2) == 'ux') {
    $html .= '
    <style type="text/css">
    #btn {
        display: block; width: 250px; height: 30px; padding: 5px 0 0 0; margin: 0 auto;
    
        background: #398525; /* viejos navegadores */
        background: -moz-linear-gradient(top, #DBDBDB 0%, #CDCDCD 100%); /* firefox */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#DBDBDB), color-stop(100%,#CDCDCD)); /* webkit */
        
        box-shadow: inset 0px 0px 6px #fff;
        -webkit-box-shadow: inset 0px 0px 6px #fff;
        border: 1px solid #DBDBDB;
        border-radius: 10px;
        
        font: bold 12px/25px Helvetica, Sans-Serif; text-align: center;
        text-transform: uppercase; text-decoration: none;
        color: #147032;
        text-shadow: 0px 1px 2px #b4d1ad;
        
        -moz-transition: color 0.25s ease-in-out;
        -webkit-transition: color 0.25s ease-in-out;
        transition: color 0.25s ease-in-out;
    }
    a.btn:hover {
        color: #145675;
        
        -moz-transition: color 0.25s ease-in-out;
        -webkit-transition: color 0.25s ease-in-out;
        transition: color 0.25s ease-in-out;
    }
    </style>
    <div id="btn">
        <a href="javascript:showDMS_UserConf();">DMS User Configuration</a>
    </div>
    ';
}
$htmlPopUp = '';
if (SYS_SKIN == 'classic') {
    G::LoadClass( 'configuration' );
    $conf = new Configurations();
    try {
        $preferencesKt = $conf->getConfiguration( 'KT_PREFERENCES', '' );
    } catch (Exception $e) {
        $preferencesKt = array ();
    }
    if (isset($preferencesKt['KT_WIN']) && $preferencesKt['KT_WIN'] != false ) {
        $htmlPopUp = '<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
            <div class="boxContentBlue">
            <table width="90%" style="margin:0px;" cellspacing="0" cellpadding="0">
            <tr>
                <td class="userGroupTitle"><a href="javascript:showDMS_UserConf();"> DMS User Configuration </a></td>
            </tr>
            </table>
            </div>
            <div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';
    }
}

$html .= '
<div>'.$htmlPopUp.'
    <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
    <div class="boxContentBlue">
    <table width="90%" style="margin:0px;" cellspacing="0" cellpadding="0">
    <tr>
        <td class="userGroupTitle">DMS Root Folder</td>
    </tr>
    </table>
    </div>
    <div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
    

    ';
  $html.="<div id='child_0'></div>
  </div>
  ";
  echo $html;
  ?>
  <script>

  </script>
