<?php
G::LoadSystem('form');
G::LoadClass ('case');

$form=$_POST['form'];

//G::pr($form);
//$oForm = new Form ( $_SESSION ['PROCESS'] . '/' . $_GET ['UID'], PATH_DYNAFORM );
//$oForm->validatePost ();

//print PMFSendVariables($_SESSION['APPLICATION'], $form);//doen't work if case has been closed
$oCase = new Cases ( );
$Fields = $oCase->loadCase ( $_SESSION ['APPLICATION'] );


//print_r($Fields);die;
//save pmconnections if any
//4942043804bb2546716d950010926706
$oForm = new Form ( $_SESSION['PROCESS'].'/'.$_SESSION['CURRENT_DYN_UID'], PATH_DYNAFORM );
//die($oForm);
//validate post for checkboxes
$oForm->validatePost();
$form=$_POST['form'];


//G::pr($Fields ['APP_DATA']);die;
//save app_data
//G::pr($Fields ['APP_DATA']);
$Fields ['APP_DATA'] = array_merge ( $Fields ['APP_DATA'], ( array ) $form );
//resguard i003


//G::pr($Fields ['APP_DATA']);
$oCase->updateCase($_SESSION ['APPLICATION'], $Fields);
foreach ( $_POST ['form'] as $sField => $sAux ) {
	
	if (isset ( $oForm->fields [$sField]->pmconnection ) && isset ( $oForm->fields [$sField]->pmfield )) {
		if (($oForm->fields [$sField]->pmconnection != '') && ($oForm->fields [$sField]->pmfield != '')) {
			if (isset ( $oForm->fields [$oForm->fields [$sField]->pmconnection] )) {
				require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
				$oAdditionalTables = new AdditionalTables ( );
				try {
					$aData = $oAdditionalTables->load ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, true );
				} catch ( Exception $oError ) {
					$aData = array ('FIELDS' => array () );
				}
				$aKeys = array ();
				$aAux = explode ( '|', $oForm->fields [$oForm->fields [$sField]->pmconnection]->keys );
				$i = 0;
				$aValues = array ();
				foreach ( $aData ['FIELDS'] as $aField ) {
					if ($aField ['FLD_KEY'] == '1') {
						$aKeys [$aField ['FLD_NAME']] = (isset ( $aAux [$i] ) ? G::replaceDataField ( $aAux [$i], $Fields ['APP_DATA'] ) : '');
						$i ++;
					}
					if ($aField ['FLD_NAME'] == $oForm->fields [$sField]->pmfield) {
						$aValues [$aField ['FLD_NAME']] = $Fields ['APP_DATA'] [$sField];
					} else {
						$aValues [$aField ['FLD_NAME']] = '';
					}
				}
				try {
					$aRow = $oAdditionalTables->getDataTable ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, $aKeys );
				} catch ( Exception $oError ) {
					$aRow = false;
				}
				if ($aRow) {
					foreach ( $aValues as $sKey => $sValue ) {
						if ($sKey != $oForm->fields [$sField]->pmfield) {
							$aValues [$sKey] = $aRow [$sKey];
						}
					}
					try {
						$oAdditionalTables->updateDataInTable ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, $aValues );
					} catch ( Exception $oError ) {
						//Nothing
					}
				} else {
					try {
						$oAdditionalTables->saveDataInTable ( $oForm->fields [$oForm->fields [$sField]->pmconnection]->pmtable, $aValues );
					} catch ( Exception $oError ) {
						//Nothing
						//print_r($oError);die;//note: could be useful to found problems with fields of the pmconnection
					}
				}
			}
		}
	}
}

 #trigger debug routines...  
 $triggers = $oCase->loadTriggers ( $_SESSION ['TASK'], 'DYNAFORM', $_SESSION['CURRENT_DYN_UID'], 'AFTER' );
 
 $_SESSION ['TRIGGER_DEBUG'] ['NUM_TRIGGERS'] = count ( $triggers );
  $_SESSION ['TRIGGER_DEBUG'] ['TIME'] = 'AFTER';
  if ($_SESSION ['TRIGGER_DEBUG'] ['NUM_TRIGGERS'] != 0) {
    $_SESSION ['TRIGGER_DEBUG'] ['TRIGGERS_NAMES'] = $oCase->getTriggerNames ( $triggers );
    $_SESSION ['TRIGGER_DEBUG'] ['TRIGGERS_VALUES'] = $triggers;
  }

  if ($_SESSION ['TRIGGER_DEBUG'] ['NUM_TRIGGERS'] != 0) {
    //Execute after triggers - Start
    $Fields ['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION ['TASK'], 'DYNAFORM', $_SESSION['CURRENT_DYN_UID'], 'AFTER', $Fields ['APP_DATA'] );
    //Execute after triggers - End
  }
 
 $oCase->updateCase($_SESSION ['APPLICATION'], $Fields);
 
 
//die;
//return to previous page
//header("Location: ".$_SERVER['HTTP_REFERER']);

?>