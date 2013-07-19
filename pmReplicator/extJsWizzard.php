<?php
$sPathToStyles=PATH_SEP.'plugin'.PATH_SEP.'tclWorkspaceManagement'.PATH_SEP;
$sPathToScripts=$sPathToStyles.'javaScripts'.PATH_SEP;
?>
<html>
 <head>
    <link href="<?php echo $sPathToStyles ?>smart_wizard_vertical.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $sPathToStyles?>jquery.dataTables.css" rel="stylesheet" type="text/css">
    <script src="<?php echo $sPathToScripts ?>jquery.js" type="text/javascript"></script>
    <script src="<?php echo $sPathToScripts ?>jquery.smartWizard.js"></script>
    <script src="<?php echo $sPathToScripts ?>jquery.dataTables.js" type="text/javascript"></script>
    <script src="<?php echo $sPathToScripts ?>WizzardExtJs.js" type="text/javascript"></script>
 </head>
 <body>
 <form action="#" method="POST">
    <div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <label class="stepNumber">1</label>
                    <spam class="stepDesc">
                        Process
                        <br />
                        <small>Process to Transfer</small>
                    </spam>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    <label class="stepNumber">2</label>
                    <spam class="stepDesc">
                        Step two
                        <br />
                        <small>Fill data in this step</small>
                    </spam>
                    
                </a>
            </li>
        </ul>
        <div id="step-1" class="content">
            <h2 class="StepTitle">Step 1: Process to trasfer</h2>
            <div id="workSpaceOrigin"></div>
            <div id="spacer"></div>
        </div>
        <div id="step-2" class="content">
            <h2 class="StepTitle">Step 2: test step</h2>
            <h1>This is a test of the wizzard</h1>
        </div>
    </div>
 </form>
 </body>
</html>